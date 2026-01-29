@extends('layouts.app')

@section('title', 'Copilot IA')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- AI Search Bar -->
    <div class="mb-6 bg-white shadow rounded-lg p-6">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <label for="searchQuery" class="block text-sm font-medium text-gray-700 mb-2">🔍 AI-Powered Smart Search</label>
                <input 
                    type="text" 
                    id="searchQuery" 
                    placeholder="Search tasks, categories, users... AI will analyze and provide insights"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
            </div>
            <button id="searchBtn" class="self-end px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium transition">
                Search
            </button>
        </div>
        <div id="searchStatus" class="text-sm text-green-600 mt-2 hidden">
            <svg class="inline animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Searching...
        </div>
    </div>

    <!-- Search Results Section -->
    <div id="searchResults" class="hidden mb-6 space-y-4">
        <!-- AI Insights -->
        <div id="aiInsights" class="bg-gradient-to-r from-purple-50 to-blue-50 border-l-4 border-purple-500 rounded-lg p-6 shadow-md">
            <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                AI Insights
            </h3>
            <div id="aiInsightsContent" class="text-gray-700 whitespace-pre-wrap"></div>
        </div>

        <!-- Results Tabs -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button class="result-tab active px-6 py-3 border-b-2 border-blue-500 text-blue-600 font-medium text-sm" data-tab="tasks">
                        Tasks <span id="tasksCount" class="ml-2 bg-blue-100 px-2 py-0.5 rounded-full text-xs">0</span>
                    </button>
                    <button class="result-tab px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-tab="categories">
                        Categories <span id="categoriesCount" class="ml-2 bg-gray-100 px-2 py-0.5 rounded-full text-xs">0</span>
                    </button>
                    <button class="result-tab px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-tab="users">
                        Users <span id="usersCount" class="ml-2 bg-gray-100 px-2 py-0.5 rounded-full text-xs">0</span>
                    </button>
                </nav>
            </div>
            
            <div class="p-6">
                <div id="tasksResults" class="result-content"></div>
                <div id="categoriesResults" class="result-content hidden"></div>
                <div id="usersResults" class="result-content hidden"></div>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar / Settings -->
        <div class="lg:w-1/2 space-y-4">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">Copilot IA</h2>
                <p class="text-sm text-gray-600 mb-4">Posez vos questions business (BI/analytics). Les données internes (tâches, catégories, utilisateurs) sont injectées automatiquement.</p>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Modèle IA</label>
                        <select id="provider" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" disabled>
                            <option value="mistral">DeepSeek R1 T2 Chimera (Free)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Modèle gratuit sans limitation</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Clé API OpenRouter (optionnel)</label>
                        <input id="apiKey" type="password" placeholder="sk-or-..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        <p class="text-xs text-gray-500 mt-1">Si laissé vide, la clé du serveur (.env) est utilisée. La clé saisie reste dans votre navigateur (localStorage).</p>
                        <div class="flex gap-2 mt-2">
                            <button id="saveKey" type="button" class="px-3 py-1.5 bg-gray-200 text-gray-800 rounded-md text-sm hover:bg-gray-300">Sauvegarder localement</button>
                            <button id="clearKey" type="button" class="px-3 py-1.5 bg-gray-200 text-gray-800 rounded-md text-sm hover:bg-gray-300">Effacer</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Préréglages rapides</label>
                        <div class="flex flex-wrap gap-2">
                            <button data-prompt="Donne-moi un résumé des tâches en retard et propose un plan d'action." class="preset-btn">Retards & plan d'action</button>
                            <button data-prompt="Quelles sont les tendances des créations et complétions de tâches sur 7 jours ?" class="preset-btn">Tendances 7 jours</button>
                            <button data-prompt="Quels utilisateurs ont le plus haut taux de complétion ?" class="preset-btn">Top productivité</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Conseils</h3>
                <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
                    <li>Pose des questions précises : "tâches en retard par catégorie"</li>
                    <li>Demande des recommandations : "comment réduire les retards"</li>
                    <li>Compare : "évolution des tâches créées vs complétées"</li>
                </ul>
            </div>
        </div>

        <!-- Chat Panel -->
        <div class="lg:w-1/2">
            <div class="bg-white shadow rounded-lg p-6 h-full flex flex-col">
                <div class="flex items-start gap-3 mb-4">
                    <div class="flex-1">
                        <label for="question" class="block text-sm font-medium text-gray-700 mb-1">Votre question</label>
                        <textarea id="question" rows="3" class="w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3" placeholder="Ex: Donne-moi le taux de complétion global et les catégories les plus en retard."></textarea>
                    </div>
                    <button id="askBtn" class="self-end px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium transition">Envoyer</button>
                </div>

                <div id="status" class="text-sm text-blue-600 mb-3 hidden">
                    <svg class="inline animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Envoi en cours...
                </div>

                <!-- Response Box -->
                <div class="flex-1 flex flex-col">
                    <div id="response" class="flex-1 border-2 border-gray-200 rounded-lg p-4 overflow-auto bg-gradient-to-b from-gray-50 to-white text-gray-800 text-sm leading-relaxed whitespace-pre-wrap" style="max-height: 500px; min-height: 300px;">
                        <span class="text-gray-400">La réponse de l'IA apparaîtra ici...</span>
                    </div>
                    <button id="expandBtn" class="hidden mt-3 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium transition self-end">
                        📄 Afficher le contenu complet
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour afficher le contenu complet -->
<div id="modalOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl w-11/12 h-5/6 flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Réponse complète de l'IA</h3>
            <button id="closeModal" class="text-gray-600 hover:text-gray-900 font-bold text-xl">✕</button>
        </div>
        <div id="modalContent" class="flex-1 p-6 overflow-auto bg-gray-50 text-gray-800 text-sm leading-relaxed whitespace-pre-wrap"></div>
        <div class="p-4 border-t border-gray-200 flex gap-2 justify-end">
            <button id="copyBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium transition">📋 Copier</button>
            <button id="closeModalBtn" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium transition">Fermer</button>
        </div>
    </div>
</div>

<script>
    const askBtn = document.getElementById('askBtn');
    const question = document.getElementById('question');
    const provider = document.getElementById('provider');
    const responseBox = document.getElementById('response');
    const statusEl = document.getElementById('status');
    const apiKeyInput = document.getElementById('apiKey');
    const saveKeyBtn = document.getElementById('saveKey');
    const clearKeyBtn = document.getElementById('clearKey');
    const expandBtn = document.getElementById('expandBtn');
    const modalOverlay = document.getElementById('modalOverlay');
    const closeModal = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modalContent = document.getElementById('modalContent');
    const copyBtn = document.getElementById('copyBtn');

    let currentResponse = '';

    // presets
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.classList.add('px-3','py-1.5','bg-gray-100','text-gray-800','rounded-md','text-sm','hover:bg-gray-200');
        btn.addEventListener('click', () => {
            question.value = btn.dataset.prompt;
            question.focus();
        });
    });

    // Modal handlers
    const openModal = () => {
        modalContent.textContent = currentResponse;
        modalOverlay.classList.remove('hidden');
    };

    const closeModalFn = () => {
        modalOverlay.classList.add('hidden');
    };

    expandBtn.addEventListener('click', openModal);
    closeModal.addEventListener('click', closeModalFn);
    closeModalBtn.addEventListener('click', closeModalFn);

    copyBtn.addEventListener('click', () => {
        navigator.clipboard.writeText(currentResponse).then(() => {
            const originalText = copyBtn.textContent;
            copyBtn.textContent = '✓ Copié !';
            setTimeout(() => {
                copyBtn.textContent = originalText;
            }, 2000);
        });
    });

    // Close modal on background click
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) closeModalFn();
    });

    const loadKey = () => {
        const k = localStorage.getItem('openrouter_api_key') || '';
        apiKeyInput.value = k;
        return k;
    };

    loadKey();

    saveKeyBtn.addEventListener('click', () => {
        localStorage.setItem('openrouter_api_key', apiKeyInput.value.trim());
        alert('Clé sauvegardée localement.');
    });

    clearKeyBtn.addEventListener('click', () => {
        localStorage.removeItem('openrouter_api_key');
        apiKeyInput.value = '';
    });

    askBtn.addEventListener('click', async () => {
        const q = question.value.trim();
        if (!q) {
            alert('Veuillez saisir une question.');
            return;
        }
        responseBox.textContent = '';
        expandBtn.classList.add('hidden');
        statusEl.classList.remove('hidden');

        try {
            const res = await fetch("{{ route('ai.query') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    query: q,
                    provider: provider.value,
                    api_key: apiKeyInput.value.trim() || null,
                })
            });

            const json = await res.json();
            statusEl.classList.add('hidden');

            if (!res.ok) {
                currentResponse = json.error || 'Erreur API';
                responseBox.textContent = currentResponse;
                expandBtn.classList.add('hidden');
                return;
            }

            let answer = '';
            if (json.response?.answer) {
                answer = json.response.answer;
            } else if (json.response) {
                answer = JSON.stringify(json.response, null, 2);
            } else {
                answer = 'Aucune réponse.';
            }

            currentResponse = answer;
            
            // Afficher un aperçu avec limitation de hauteur
            responseBox.textContent = answer;
            
            // Afficher le bouton "Afficher plus" si le contenu dépasse
            if (responseBox.scrollHeight > responseBox.clientHeight) {
                expandBtn.classList.remove('hidden');
            } else {
                expandBtn.classList.add('hidden');
            }
        } catch (e) {
            statusEl.classList.add('hidden');
            currentResponse = 'Erreur: ' + e.message;
            responseBox.textContent = currentResponse;
            expandBtn.classList.add('hidden');
        }
    });

    // AI Search Functionality
    const searchQuery = document.getElementById('searchQuery');
    const searchBtn = document.getElementById('searchBtn');
    const searchStatus = document.getElementById('searchStatus');
    const searchResults = document.getElementById('searchResults');
    const aiInsights = document.getElementById('aiInsights');
    const aiInsightsContent = document.getElementById('aiInsightsContent');
    
    const resultTabs = document.querySelectorAll('.result-tab');
    const tasksResults = document.getElementById('tasksResults');
    const categoriesResults = document.getElementById('categoriesResults');
    const usersResults = document.getElementById('usersResults');
    
    const tasksCount = document.getElementById('tasksCount');
    const categoriesCount = document.getElementById('categoriesCount');
    const usersCount = document.getElementById('usersCount');

    // Tab switching
    resultTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.dataset.tab;
            
            // Update tabs
            resultTabs.forEach(t => {
                t.classList.remove('active', 'border-blue-500', 'text-blue-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            tab.classList.add('active', 'border-blue-500', 'text-blue-600');
            tab.classList.remove('border-transparent', 'text-gray-500');
            
            // Update content
            document.querySelectorAll('.result-content').forEach(c => c.classList.add('hidden'));
            document.getElementById(targetTab + 'Results').classList.remove('hidden');
        });
    });

    // Search functionality
    async function performSearch() {
        const query = searchQuery.value.trim();
        if (!query) {
            alert('Please enter a search query');
            return;
        }

        searchBtn.disabled = true;
        searchStatus.classList.remove('hidden');
        searchResults.classList.add('hidden');

        try {
            const response = await fetch("{{ route('ai.search') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ query })
            });

            const data = await response.json();
            
            if (!response.ok) {
                alert(data.error || 'Search failed');
                return;
            }

            // Display AI insights
            aiInsightsContent.textContent = data.ai_insights || 'No insights available';
            
            // Update counts
            tasksCount.textContent = data.results.tasks?.length || 0;
            categoriesCount.textContent = data.results.categories?.length || 0;
            usersCount.textContent = data.results.users?.length || 0;
            
            // Display tasks
            if (data.results.tasks?.length) {
                tasksResults.innerHTML = data.results.tasks.map(task => `
                    <div class="border-b pb-4 mb-4 last:border-b-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-lg text-gray-900">
                                    <a href="${task.url}" class="hover:text-blue-600">${escapeHtml(task.title)}</a>
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">${escapeHtml(task.description || 'No description')}</p>
                                <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                    <span class="px-2 py-1 rounded ${getStatusClass(task.status)}">${task.status}</span>
                                    ${task.due_date ? `<span>📅 ${task.due_date}</span>` : ''}
                                    ${task.overdue ? '<span class="text-red-600 font-semibold">⚠️ Overdue</span>' : ''}
                                    ${task.category ? `<span>📁 ${escapeHtml(task.category.name)}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                tasksResults.innerHTML = '<p class="text-gray-500 text-center py-8">No tasks found</p>';
            }
            
            // Display categories
            if (data.results.categories?.length) {
                categoriesResults.innerHTML = data.results.categories.map(cat => `
                    <div class="border-b pb-4 mb-4 last:border-b-0">
                        <div class="flex items-start gap-3">
                            <div class="w-4 h-4 rounded-full mt-1" style="background-color: ${cat.color}"></div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-lg text-gray-900">
                                    <a href="${cat.url}" class="hover:text-blue-600">${escapeHtml(cat.name)}</a>
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">${escapeHtml(cat.description || 'No description')}</p>
                                <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                    <span>📊 ${cat.tasks_count} tasks (${cat.completed_tasks} completed)</span>
                                    <span>👤 ${escapeHtml(cat.user.name)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                categoriesResults.innerHTML = '<p class="text-gray-500 text-center py-8">No categories found</p>';
            }
            
            // Display users
            if (data.results.users?.length) {
                usersResults.innerHTML = data.results.users.map(user => `
                    <div class="border-b pb-4 mb-4 last:border-b-0">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-lg text-gray-900">${escapeHtml(user.name)}</h4>
                                <p class="text-sm text-gray-600">${escapeHtml(user.email)}</p>
                                <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                    <span class="px-2 py-1 rounded ${user.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700'}">${user.role}</span>
                                    <span>📝 ${user.tasks_count} tasks (${user.completed_tasks} completed)</span>
                                    <span>📁 ${user.categories_count} categories</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                usersResults.innerHTML = '<p class="text-gray-500 text-center py-8">No users found</p>';
            }
            
            searchResults.classList.remove('hidden');
            
        } catch (error) {
            alert('Search error: ' + error.message);
        } finally {
            searchBtn.disabled = false;
            searchStatus.classList.add('hidden');
        }
    }

    searchBtn.addEventListener('click', performSearch);
    searchQuery.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') performSearch();
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getStatusClass(status) {
        const classes = {
            'pending': 'bg-yellow-100 text-yellow-700',
            'in_progress': 'bg-blue-100 text-blue-700',
            'completed': 'bg-green-100 text-green-700',
            'cancelled': 'bg-red-100 text-red-700'
        };
        return classes[status] || 'bg-gray-100 text-gray-700';
    }
</script>
@endsection
