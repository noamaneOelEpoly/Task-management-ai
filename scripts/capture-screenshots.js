import puppeteer from 'puppeteer';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration
const BASE_URL = 'http://localhost:8000';
const SCREENSHOTS_DIR = path.join(__dirname, '..', 'screenshots');

// User credentials
const USER_CREDENTIALS = {
  email: 'test@example.com',
  password: 'password'
};

// Admin credentials
const ADMIN_CREDENTIALS = {
  email: 'admin@root.com',
  password: 'password'
};

// Create screenshots directory if it doesn't exist
if (!fs.existsSync(SCREENSHOTS_DIR)) {
  fs.mkdirSync(SCREENSHOTS_DIR, { recursive: true });
}

// Pages to capture
const pagesToCapture = [
  // Public pages
  {
    name: '01-login-page.png',
    description: 'Login Page',
    url: '/login',
    waitForSelector: '#email',
    requiresLogin: false
  },
  
  // Regular user pages
  {
    name: '02-user-dashboard.png',
    description: 'User Dashboard - Tasks List',
    url: '/tasks',
    waitForSelector: 'body',
    requiresLogin: 'user'
  },
  {
    name: '03-user-task-details.png',
    description: 'User - Task Details View',
    url: '/tasks/1',
    waitForSelector: 'body',
    requiresLogin: 'user'
  },
  {
    name: '04-user-create-task.png',
    description: 'User - Create Task Form',
    url: '/tasks/create',
    waitForSelector: 'body',
    requiresLogin: 'user'
  },
  {
    name: '05-user-edit-task.png',
    description: 'User - Edit Task Form',
    url: '/tasks/1/edit',
    waitForSelector: 'body',
    requiresLogin: 'user'
  },
  {
    name: '06-user-categories-list.png',
    description: 'User - Categories List',
    url: '/categories',
    waitForSelector: 'body',
    requiresLogin: 'user'
  },
  {
    name: '07-user-category-details.png',
    description: 'User - Category Details with Tasks',
    url: '/categories/1',
    waitForSelector: 'body',
    requiresLogin: 'user'
  },
  {
    name: '08-user-create-category.png',
    description: 'User - Create Category Form',
    url: '/categories/create',
    waitForSelector: 'body',
    requiresLogin: 'user'
  },
  {
    name: '09-user-edit-category.png',
    description: 'User - Edit Category Form',
    url: '/categories/1/edit',
    waitForSelector: 'body',
    requiresLogin: 'user'
  },
  {
    name: '10-user-profile.png',
    description: 'User - Profile Settings',
    url: '/profile',
    waitForSelector: 'body',
    requiresLogin: 'user'
  },
  {
    name: '11-user-ai-assistant.png',
    description: 'User - AI Assistant Interface',
    url: '/ai',
    waitForSelector: 'body',
    requiresLogin: 'user'
  },
  {
    name: '12-ai-search-interface.png',
    description: 'AI Assistant - Smart Search Interface',
    url: '/ai',
    waitForSelector: '#searchQuery',
    requiresLogin: 'user',
    additionalActions: async (page) => {
      // Type a search query
      await page.type('#searchQuery', 'urgent tasks');
      // Click search button
      await page.click('#searchBtn');
      // Wait for results to appear
      await page.waitForSelector('#searchResults', { timeout: 10000 }).catch(() => {
        console.log('  Search results may not have appeared');
      });
      await new Promise(resolve => setTimeout(resolve, 2000)); // Wait for animations
    }
  },
  
  // Admin pages
  {
    name: '13-admin-dashboard.png',
    description: 'Admin - Main Dashboard',
    url: '/admin/dashboard',
    waitForSelector: 'body',
    requiresLogin: 'admin'
  },
  {
    name: '14-admin-users-list.png',
    description: 'Admin - Users Management',
    url: '/admin/users',
    waitForSelector: 'body',
    requiresLogin: 'admin'
  },
  {
    name: '15-admin-user-details.png',
    description: 'Admin - View User Details',
    url: '/admin/users/2',
    waitForSelector: 'body',
    requiresLogin: 'admin'
  },
  {
    name: '16-admin-edit-user.png',
    description: 'Admin - Edit User',
    url: '/admin/users/2/edit',
    waitForSelector: 'body',
    requiresLogin: 'admin'
  },
  {
    name: '17-admin-tasks-management.png',
    description: 'Admin - All Tasks Management',
    url: '/admin/tasks',
    waitForSelector: 'body',
    requiresLogin: 'admin'
  },
  {
    name: '18-admin-categories-management.png',
    description: 'Admin - All Categories Management',
    url: '/admin/categories',
    waitForSelector: 'body',
    requiresLogin: 'admin'
  }
];

async function login(page, userType = 'user') {
  try {
    const credentials = userType === 'admin' ? ADMIN_CREDENTIALS : USER_CREDENTIALS;
    
    console.log(`  Logging in as ${userType}...`);
    await page.goto(BASE_URL + '/login', { waitUntil: 'networkidle2', timeout: 10000 });
    
    // Wait for the form to be visible
    await page.waitForSelector('#email', { timeout: 5000 });
    await page.waitForSelector('#password', { timeout: 5000 });
    
    // Clear and fill the form
    await page.click('#email', { clickCount: 3 });
    await page.type('#email', credentials.email);
    await page.click('#password', { clickCount: 3 });
    await page.type('#password', credentials.password);
    
    // Wait a bit before submitting
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // Submit
    await page.click('button[type="submit"]');
    
    // Wait for navigation with longer timeout
    try {
      await page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 15000 });
    } catch (navError) {
      // Check if we're still on the login page
      const currentUrl = page.url();
      if (currentUrl.includes('/login')) {
        throw new Error(`Login failed as ${userType} - still on login page. Check credentials in database.`);
      }
      // If we navigated but timed out, that's okay
      console.log('  Navigation completed (timeout but page changed)');
    }
    
    // Check if we're actually logged in (should be redirected away from login page)
    const currentUrl = page.url();
    if (currentUrl.includes('/login')) {
      throw new Error(`Login failed as ${userType} - still on login page. Verify user exists: php artisan db:seed`);
    }
    
    console.log(`  Login successful as ${userType}`);
  } catch (error) {
    console.error(`  Login error (${userType}):`, error.message);
    throw error;
  }
}

async function logout(page) {
  try {
    console.log('  Logging out...');
    
    // Try to find and click the logout button/link
    try {
      // Look for common logout patterns
      const logoutSelectors = [
        'form[action*="logout"] button',
        'button[type="submit"][form*="logout"]',
        'a[href*="logout"]'
      ];
      
      for (const selector of logoutSelectors) {
        const element = await page.$(selector);
        if (element) {
          await element.click();
          await page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 5000 }).catch(() => {});
          console.log('  Logged out successfully');
          return;
        }
      }
    } catch (error) {
      // If clicking fails, clear cookies as fallback
      const client = await page.target().createCDPSession();
      await client.send('Network.clearBrowserCookies');
      console.log('  Logged out (cookies cleared)');
    }
  } catch (error) {
    console.error('  Logout error:', error.message);
  }
}

async function captureScreenshot(page, pageConfig) {
  try {
    console.log('Capturing: ' + pageConfig.description);
    
    // Navigate to page
    await page.goto(BASE_URL + pageConfig.url, { 
      waitUntil: 'networkidle2', 
      timeout: 15000 
    }).catch(async (error) => {
      // If page doesn't exist (404), capture it anyway
      console.log('  Page may not exist, capturing current state...');
    });
    
    // Wait for selector
    if (pageConfig.waitForSelector) {
      await page.waitForSelector(pageConfig.waitForSelector, { timeout: 5000 }).catch(() => {
        console.log('  Selector not found, continuing...');
      });
    }
    
    // Execute additional actions if specified
    if (pageConfig.additionalActions) {
      try {
        await pageConfig.additionalActions(page);
      } catch (error) {
        console.log('  Additional actions error:', error.message);
      }
    }
    
    // Wait for rendering
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Take screenshot
    await page.screenshot({
      path: path.join(SCREENSHOTS_DIR, pageConfig.name),
      fullPage: true
    });
    
    console.log('  ✓ Saved: ' + pageConfig.name);
  } catch (error) {
    console.error('  ✗ Error capturing ' + pageConfig.name + ':', error.message);
  }
}

async function generateScreenshots() {
  console.log('\nStarting screenshot capture...\n');
  console.log('Credentials configured:');
  console.log('  User: ' + USER_CREDENTIALS.email);
  console.log('  Admin: ' + ADMIN_CREDENTIALS.email + '\n');
  
  let browser;
  try {
    browser = await puppeteer.launch({
      headless: 'new',
      args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 720 });
    
    // Enable console logging from the page
    page.on('console', msg => {
      if (msg.type() === 'error') {
        console.log('  Browser error:', msg.text());
      }
    });
    
    // Check if server is accessible
    try {
      await page.goto(BASE_URL, { waitUntil: 'networkidle2', timeout: 30000 });
    } catch (error) {
      console.error('\nERROR: Laravel server is not accessible at ' + BASE_URL);
      console.error('Make sure the Laravel server is running with:');
      console.error('  php artisan serve');
      console.error('\nAlso make sure the database is seeded with test user:');
      console.error('  php artisan migrate:fresh --seed\n');
      await browser.close();
      process.exit(1);
    }
    
    let currentUser = null;
    
    // Capture pages
    for (const pageConfig of pagesToCapture) {
      // Handle login requirements
      if (pageConfig.requiresLogin === false) {
        // Public page - logout if needed
        if (currentUser) {
          await logout(page);
          currentUser = null;
        }
      } else if (pageConfig.requiresLogin === 'user' && currentUser !== 'user') {
        // Need user login
        if (currentUser) {
          await logout(page);
        }
        await login(page, 'user');
        currentUser = 'user';
      } else if (pageConfig.requiresLogin === 'admin' && currentUser !== 'admin') {
        // Need admin login
        if (currentUser) {
          await logout(page);
        }
        await login(page, 'admin');
        currentUser = 'admin';
      }
      
      await captureScreenshot(page, pageConfig);
    }
    
    await browser.close();
    
    console.log('\n✓ SUCCESS: All screenshots generated!');
    console.log('Location: screenshots/\n');
    
  } catch (error) {
    console.error('\n✗ ERROR:', error.message);
    if (browser) await browser.close();
    process.exit(1);
  }
}

generateScreenshots();
