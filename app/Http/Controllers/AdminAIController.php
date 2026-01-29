<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AdminAIController extends Controller
{
    /**
     * Show AI Assistant dashboard
     */
    public function index()
    {
        $stats = $this->getOverallStats();
        return view('ai.assistant', compact('stats'));
    }

    /**
     * Process AI query
     */
    public function query(Request $request)
    {
        $query = $request->string('query')->trim();
        $provider = $request->input('provider', 'mistral'); // mistral | deepseek
        $userApiKey = $request->string('api_key')->trim();
        
        \Log::info('AI Query Request', [
            'provider_input' => $request->input('provider'),
            'provider_resolved' => $provider,
            'has_api_key' => !empty($userApiKey->toString()),
        ]);
        
        if ($query->isEmpty()) {
            return response()->json(['error' => 'Requête vide'], 400);
        }

        $stats = $this->getOverallStats();
        $analytics = $this->getDetailedAnalytics();

        $response = $this->callOpenRouter(
            prompt: $query->toString(),
            provider: $provider,
            stats: $stats,
            analytics: $analytics,
            userApiKey: $userApiKey->isNotEmpty() ? $userApiKey->toString() : null,
        );
        
        return response()->json([
            'query' => $query,
            'provider' => $provider,
            'response' => $response,
            'timestamp' => now()
        ]);
    }

    /**
     * Get analytics data
     */
    public function analytics()
    {
        $stats = $this->getDetailedAnalytics();
        return response()->json($stats);
    }

    /**
     * AI-powered search across tasks, categories, and users
     */
    public function search(Request $request)
    {
        $query = $request->string('query')->trim();
        $provider = $request->input('provider', 'mistral');
        $userApiKey = $request->string('api_key')->trim();
        
        if ($query->isEmpty()) {
            return response()->json(['error' => 'Search query is empty'], 400);
        }

        // Perform comprehensive search
        $searchResults = $this->performIntelligentSearch($query->toString());
        
        // Get AI interpretation and recommendations
        $aiResponse = $this->callOpenRouterForSearch(
            query: $query->toString(),
            provider: $provider,
            searchResults: $searchResults,
            userApiKey: $userApiKey->isNotEmpty() ? $userApiKey->toString() : null,
        );
        
        return response()->json([
            'query' => $query,
            'results' => $searchResults,
            'ai_insights' => $aiResponse,
            'timestamp' => now()
        ]);
    }

    /**
     * Perform intelligent search across all models
     */
    private function performIntelligentSearch(string $query): array
    {
        $query = strtolower($query);
        $results = [];
        
        // Search Tasks
        $tasks = Task::with(['user', 'category'])
            ->where(function($q) use ($query) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$query}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$query}%"])
                  ->orWhereRaw('LOWER(status) LIKE ?', ["%{$query}%"]);
            })
            ->limit(20)
            ->get()
            ->map(function($task) {
                return [
                    'type' => 'task',
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'due_date' => $task->due_date?->format('Y-m-d'),
                    'completed' => $task->isCompleted(),
                    'overdue' => $task->isOverdue(),
                    'user' => $task->user->name,
                    'category' => $task->category->name ?? 'Uncategorized',
                    'url' => route('tasks.show', $task->id)
                ];
            });
        
        // Search Categories
        $categories = Category::with(['user', 'tasks'])
            ->where(function($q) use ($query) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$query}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$query}%"]);
            })
            ->limit(20)
            ->get()
            ->map(function($category) {
                return [
                    'type' => 'category',
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'color' => $category->color,
                    'tasks_count' => $category->tasks()->count(),
                    'completed_tasks' => $category->tasks()->whereNotNull('completed_at')->count(),
                    'user' => $category->user->name,
                    'url' => route('categories.show', $category->id)
                ];
            });
        
        // Search Users
        $users = User::with(['tasks', 'categories'])
            ->where(function($q) use ($query) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$query}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$query}%"]);
            })
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'type' => 'user',
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'tasks_count' => $user->tasks()->count(),
                    'completed_tasks' => $user->tasks()->whereNotNull('completed_at')->count(),
                    'categories_count' => $user->categories()->count(),
                    'url' => auth()->user()->role === 'admin' ? route('admin.users.show', $user->id) : null
                ];
            });
        
        return [
            'tasks' => $tasks->values()->toArray(),
            'categories' => $categories->values()->toArray(),
            'users' => $users->values()->toArray(),
            'total_results' => $tasks->count() + $categories->count() + $users->count(),
            'query' => $query
        ];
    }

    /**
     * Call OpenRouter for search interpretation
     */
    private function callOpenRouterForSearch(string $query, string $provider, array $searchResults, ?string $userApiKey = null): array
    {
        $apiKey = $userApiKey ?: config('services.openrouter.key');
        if (empty($apiKey)) {
            return [
                'error' => 'OpenRouter API key missing',
            ];
        }

        $model = 'tngtech/deepseek-r1t2-chimera:free';

        $system = "You are an intelligent search assistant. Analyze search results and provide insights, patterns, and recommendations. Be concise and actionable. Respond in the same language as the query.";

        try {
            $http = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name', 'Task Management'),
            ]);

            $caBundle = config('services.openrouter.ca_bundle');
            if (!empty($caBundle)) {
                $http = $http->withOptions(['verify' => $caBundle]);
            }

            $response = $http->timeout(30)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    [
                        'role' => 'user',
                        'content' => "Search Query: {$query}\n\nSearch Results:\n" . json_encode($searchResults, JSON_PRETTY_PRINT) . "\n\nProvide a brief summary of findings, key patterns, and actionable recommendations.",
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            if ($response->failed()) {
                \Log::error('OpenRouter Search API Failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                return ['error' => 'OpenRouter API call failed', 'details' => $response->json()];
            }

            $jsonResponse = $response->json();
            $content = data_get($jsonResponse, 'choices.0.message.content', '');
            
            \Log::debug('OpenRouter Search Response', [
                'content_length' => strlen($content),
                'full_response' => $jsonResponse,
            ]);
            
            return [
                'model' => data_get($jsonResponse, 'model', 'unknown'),
                'summary' => $content ?: 'No response from AI model',
            ];
        } catch (\Throwable $e) {
            return [
                'error' => 'Exception during OpenRouter call',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process AI query and return response
     */
    private function processQuery(string $query): array
    {
        $query = strtolower($query);
        
        // Statistics queries
        if ($this->matchesPattern($query, ['statistique', 'stats', 'combien', 'total', 'nombre'])) {
            return $this->handleStatisticsQuery($query);
        }
        
        // Search queries
        if ($this->matchesPattern($query, ['recherche', 'trouve', 'cherche', 'search', 'find'])) {
            return $this->handleSearchQuery($query);
        }
        
        // Productivity queries
        if ($this->matchesPattern($query, ['productiv', 'rendement', 'performance', 'efficac', 'progress'])) {
            return $this->handleProductivityQuery($query);
        }
        
        // Trend queries
        if ($this->matchesPattern($query, ['tendance', 'trend', 'évolution', 'progression', 'augment', 'baiss'])) {
            return $this->handleTrendQuery($query);
        }
        
        // Recommendation queries
        if ($this->matchesPattern($query, ['recommand', 'suggest', 'conseil', 'aide', 'besoin', 'retard'])) {
            return $this->handleRecommendationQuery($query);
        }
        
        // Default: General statistics
        return $this->getOverallStats();
    }

    /**
     * Call OpenRouter with Mistral or DeepSeek
     */
    private function callOpenRouter(string $prompt, string $provider, array $stats, array $analytics, ?string $userApiKey = null): array
    {
        $apiKey = $userApiKey ?: config('services.openrouter.key');
        if (empty($apiKey)) {
            return [
                'error' => 'Clé API OpenRouter manquante. Ajoutez OPENROUTER_API_KEY dans votre .env.',
            ];
        }

        $model = 'tngtech/deepseek-r1t2-chimera:free';

        $system = "Tu es un assistant Business Intelligence. Tu peux répondre en français, fournir des métriques, tendances et recommandations. Utilise les données fournies dans le contexte pour tes calculs. Si une donnée manque, précise-le.";

        $context = [
            'stats' => $stats,
            'analytics' => $analytics,
        ];

        try {
            // Debug logging
            \Log::debug('OpenRouter Request', [
                'api_key_present' => !empty($apiKey),
                'api_key_length' => strlen($apiKey),
                'api_key_starts_with' => substr($apiKey, 0, 10),
                'provider' => $provider,
                'model' => $model,
            ]);

            $http = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name', 'Taskem Management'),
            ]);

            // Allow overriding CA bundle when the global PHP trust store is missing
            $caBundle = config('services.openrouter.ca_bundle');
            if (!empty($caBundle)) {
                $http = $http->withOptions(['verify' => $caBundle]);
            }

            $response = $http->timeout(30)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    [
                        'role' => 'user',
                        'content' => "Question: {$prompt}\nContexte (JSON): " . json_encode($context),
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

            if ($response->failed()) {
                \Log::error('OpenRouter API Failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                return [
                    'error' => 'Appel OpenRouter échoué',
                    'status' => $response->status(),
                    'body' => $response->json(),
                ];
            }

            $jsonResponse = $response->json();
            $content = data_get($jsonResponse, 'choices.0.message.content', '');
            $actualModel = data_get($jsonResponse, 'model', 'unknown');

            \Log::debug('OpenRouter Response', [
                'actual_model' => $actualModel,
                'content_length' => strlen($content),
                'full_response' => $jsonResponse,
            ]);

            return [
                'model' => $actualModel,
                'answer' => $content,
            ];
        } catch (\Throwable $e) {
            return [
                'error' => 'Exception lors de l\'appel OpenRouter',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if query matches patterns
     */
    private function matchesPattern(string $query, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (strpos($query, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Handle statistics queries
     */
    private function handleStatisticsQuery(string $query): array
    {
        $stats = $this->getOverallStats();
        
        if ($this->matchesPattern($query, ['utilisateur', 'user', 'account'])) {
            return [
                'type' => 'statistics',
                'title' => 'Statistiques des Utilisateurs',
                'data' => [
                    'Total utilisateurs' => $stats['total_users'],
                    'Admins' => $stats['total_admins'],
                    'Utilisateurs réguliers' => $stats['total_regular_users'],
                    'Utilisateurs actifs (7 jours)' => $stats['active_users_7d'],
                ]
            ];
        }
        
        if ($this->matchesPattern($query, ['tâche', 'task', 'todo'])) {
            return [
                'type' => 'statistics',
                'title' => 'Statistiques des Tâches',
                'data' => [
                    'Total tâches' => $stats['total_tasks'],
                    'Complétées' => $stats['completed_tasks'],
                    'En cours' => $stats['pending_tasks'],
                    'En retard' => $stats['overdue_tasks'],
                    'Taux de complétion' => round($stats['completion_rate'], 2) . '%',
                ]
            ];
        }
        
        if ($this->matchesPattern($query, ['catégorie', 'category'])) {
            return [
                'type' => 'statistics',
                'title' => 'Statistiques des Catégories',
                'data' => [
                    'Total catégories' => $stats['total_categories'],
                    'Catégorie plus utilisée' => $stats['most_used_category']['name'] ?? 'N/A',
                    'Tâches dans cette catégorie' => $stats['most_used_category']['count'] ?? 0,
                ]
            ];
        }
        
        return ['type' => 'statistics', 'title' => 'Vue d\'ensemble', 'data' => $stats];
    }

    /**
     * Handle search queries
     */
    private function handleSearchQuery(string $query): array
    {
        preg_match('/(?:recherche|cherche|find|search)\s+(.+)/i', $query, $matches);
        $searchTerm = $matches[1] ?? '';
        
        if (empty($searchTerm)) {
            return [
                'type' => 'search',
                'title' => 'Recherche',
                'message' => 'Veuillez préciser votre recherche (ex: "recherche tâche importante")'
            ];
        }
        
        $tasks = Task::where('title', 'LIKE', "%$searchTerm%")
                     ->orWhere('description', 'LIKE', "%$searchTerm%")
                     ->with(['user', 'category'])
                     ->limit(5)
                     ->get();
        
        $categories = Category::where('name', 'LIKE', "%$searchTerm%")
                             ->orWhere('description', 'LIKE', "%$searchTerm%")
                             ->with('user')
                             ->limit(5)
                             ->get();
        
        return [
            'type' => 'search',
            'title' => 'Résultats de recherche pour: ' . $searchTerm,
            'tasks' => $tasks->map(fn($t) => [
                'titre' => $t->title,
                'utilisateur' => $t->user->name,
                'catégorie' => $t->category?->name ?? 'Sans catégorie',
                'statut' => $t->completed_at ? 'Complétée' : 'En cours'
            ]),
            'categories' => $categories->map(fn($c) => [
                'nom' => $c->name,
                'propriétaire' => $c->user->name,
                'tâches' => $c->tasks->count()
            ])
        ];
    }

    /**
     * Handle productivity queries
     */
    private function handleProductivityQuery(string $query): array
    {
        $stats = $this->getDetailedAnalytics();
        
        return [
            'type' => 'productivity',
            'title' => 'Analyse de Productivité',
            'data' => [
                'Taux de complétion global' => round($stats['completion_rate'], 2) . '%',
                'Tâches complétées cette semaine' => $stats['completed_this_week'],
                'Tâches créées cette semaine' => $stats['created_this_week'],
                'Temps moyen de complétion' => $stats['avg_completion_time'] . ' jour(s)',
                'Utilisateur le plus productif' => $stats['top_user']['name'] ?? 'N/A',
                'Tâches complétées (top user)' => $stats['top_user']['completed'] ?? 0,
            ]
        ];
    }

    /**
     * Handle trend queries
     */
    private function handleTrendQuery(string $query): array
    {
        $trends = $this->calculateTrends();
        
        return [
            'type' => 'trends',
            'title' => 'Tendances et Évolution',
            'data' => [
                'Tendance des tâches (7 jours)' => $trends['task_trend'],
                'Tâches par jour (moyenne)' => round($trends['avg_tasks_per_day'], 2),
                'Taux de complétion (tendance)' => $trends['completion_trend'],
                'Croissance des utilisateurs' => $trends['user_growth'] . '%',
            ],
            'chart_data' => $trends['daily_tasks_last_7_days']
        ];
    }

    /**
     * Handle recommendation queries
     */
    private function handleRecommendationQuery(string $query): array
    {
        $recommendations = $this->generateRecommendations();
        
        return [
            'type' => 'recommendations',
            'title' => 'Recommandations et Insights',
            'recommendations' => $recommendations
        ];
    }

    /**
     * Get overall statistics
     */
    private function getOverallStats(): array
    {
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalTasks = Task::count();
        $completedTasks = Task::whereNotNull('completed_at')->count();
        $pendingTasks = Task::whereNull('completed_at')->count();
        $overdueTasks = Task::whereNull('completed_at')
                            ->where('due_date', '<', now())
                            ->count();
        $totalCategories = Category::count();
        $mostUsedCategory = Category::withCount('tasks')
                                   ->orderBy('tasks_count', 'desc')
                                   ->first();
        
        $activeUsers7d = User::where('updated_at', '>=', now()->subDays(7))->count();
        
        return [
            'total_users' => $totalUsers,
            'total_admins' => $totalAdmins,
            'total_regular_users' => $totalUsers - $totalAdmins,
            'active_users_7d' => $activeUsers7d,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'overdue_tasks' => $overdueTasks,
            'completion_rate' => $totalTasks > 0 ? ($completedTasks / $totalTasks * 100) : 0,
            'total_categories' => $totalCategories,
            'most_used_category' => $mostUsedCategory ? [
                'name' => $mostUsedCategory->name,
                'count' => $mostUsedCategory->tasks_count
            ] : null,
        ];
    }

    /**
     * Get detailed analytics
     */
    private function getDetailedAnalytics(): array
    {
        $stats = $this->getOverallStats();
        
        $completedThisWeek = Task::whereNotNull('completed_at')
                                 ->where('completed_at', '>=', now()->subDays(7))
                                 ->count();
        
        $createdThisWeek = Task::where('created_at', '>=', now()->subDays(7))->count();
        
        $topUser = User::withCount('tasks')
                       ->where('role', 'user')
                       ->orderBy('tasks_count', 'desc')
                       ->first();
        
        $avgCompletionTime = Task::whereNotNull('completed_at')
                                 ->get()
                                 ->average(function ($task) {
                                     return $task->created_at->diffInDays($task->updated_at);
                                 });
        
        return array_merge($stats, [
            'completed_this_week' => $completedThisWeek,
            'created_this_week' => $createdThisWeek,
            'avg_completion_time' => round($avgCompletionTime),
            'top_user' => $topUser ? [
                'name' => $topUser->name,
                'completed' => $topUser->tasks->where('completed_at', '!=', null)->count()
            ] : null,
        ]);
    }

    /**
     * Calculate trends
     */
    private function calculateTrends(): array
    {
        $today = now();
        $last7Days = collect();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->clone()->subDays($i);
            $count = Task::whereDate('created_at', $date)->count();
            $last7Days->push([
                'date' => $date->format('d/m'),
                'count' => $count
            ]);
        }
        
        $avgTasksLastWeek = $last7Days->avg('count');
        $totalTasksLastWeek = $last7Days->sum('count');
        $taskTrend = $avgTasksLastWeek > 0 ? 'En hausse ↑' : 'En baisse ↓';
        
        $completionRateLastWeek = Task::where('created_at', '>=', $today->subDays(7))
                                      ->avg(function ($task) {
                                          return $task->completed_at ? 100 : 0;
                                      });
        
        $usersLastWeek = User::where('created_at', '>=', $today->clone()->subDays(7))->count();
        $usersWeekBefore = User::whereBetween('created_at', [
            $today->clone()->subDays(14),
            $today->clone()->subDays(7)
        ])->count();
        
        $userGrowth = $usersWeekBefore > 0 ? (($usersLastWeek - $usersWeekBefore) / $usersWeekBefore * 100) : 0;
        
        return [
            'task_trend' => $taskTrend,
            'avg_tasks_per_day' => $avgTasksLastWeek,
            'completion_trend' => round($completionRateLastWeek, 2) . '%',
            'user_growth' => round($userGrowth, 2),
            'daily_tasks_last_7_days' => $last7Days->values()->toArray()
        ];
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(): array
    {
        $recommendations = [];
        
        $overdueTasks = Task::whereNull('completed_at')
                           ->where('due_date', '<', now())
                           ->count();
        
        if ($overdueTasks > 5) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => "Attention! Il y a $overdueTasks tâches en retard. Priorisez leur complétion."
            ];
        }
        
        $completionRate = $this->getOverallStats()['completion_rate'];
        if ($completionRate < 50) {
            $recommendations[] = [
                'type' => 'info',
                'message' => "Le taux de complétion est à " . round($completionRate, 1) . "%. Encouragez l'équipe à compléter les tâches."
            ];
        }
        
        $inactiveUsers = User::where('updated_at', '<', now()->subDays(30))
                            ->where('role', 'user')
                            ->count();
        
        if ($inactiveUsers > 0) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => "$inactiveUsers utilisateur(s) n'ont pas été actif(s) depuis 30 jours."
            ];
        }
        
        $emptyCategories = Category::withCount('tasks')
                                  ->having('tasks_count', '=', 0)
                                  ->count();
        
        if ($emptyCategories > 0) {
            $recommendations[] = [
                'type' => 'info',
                'message' => "$emptyCategories catégorie(s) vide(s) peut/peuvent être supprimée(s)."
            ];
        }
        
        $tasksPerDay = Task::where('created_at', '>=', now()->subDays(7))
                          ->count() / 7;
        
        if ($tasksPerDay > 20) {
            $recommendations[] = [
                'type' => 'success',
                'message' => "Excellent! L'équipe est très productive avec " . round($tasksPerDay) . " tâches/jour en moyenne."
            ];
        }
        
        if (empty($recommendations)) {
            $recommendations[] = [
                'type' => 'success',
                'message' => 'Tout fonctionne bien! Continuez ainsi.'
            ];
        }
        
        return $recommendations;
    }
}
