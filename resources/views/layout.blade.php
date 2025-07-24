<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SMS Dev')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📱</text></svg>">
    
    <style>
        /* Styles personnalisés */
        .container {
            max-width: 1200px;
        }

        /* Animation pour les notifications */
        .notification {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Styles pour la mailbox */
        .sms-sidebar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .sms-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sms-sidebar::-webkit-scrollbar-track {
            background: #f7fafc;
        }

        .sms-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        .sms-sidebar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        /* Transition pour les éléments sélectionnés */
        .sms-item {
            transition: all 0.2s ease;
        }

        .sms-item:hover {
            transform: translateX(2px);
        }
        
        /* Style pour les détails */
        details[open] summary {
            margin-bottom: 0.75rem;
        }
        
        /* Style pour le code */
        code {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        }
        
        /* Style pour les liens de pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }
        
        .pagination a,
        .pagination span {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            text-decoration: none;
            color: #374151;
            background-color: white;
            transition: all 0.2s;
        }
        
        .pagination a:hover {
            background-color: #f3f4f6;
            border-color: #9ca3af;
        }
        
        .pagination .current {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .pagination .disabled {
            color: #9ca3af;
            cursor: not-allowed;
        }
        
        .pagination .disabled:hover {
            background-color: white;
            border-color: #d1d5db;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    

    <!-- Contenu principal -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <div>
                    <p>&copy; {{ date('Y') }} SMS Dev Package - Development and debugging SMS</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span>Laravel {{ app()->version() }}</span>
                    <span>•</span>
                    <span>PHP {{ PHP_VERSION }}</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Auto-masquer les notifications après 5 secondes
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(function(notification) {
                setTimeout(function() {
                    notification.style.opacity = '0';
                    setTimeout(function() {
                        notification.remove();
                    }, 300);
                }, 5000);
            });
        });

        // Confirmation pour les actions destructives
        document.addEventListener('DOMContentLoaded', function() {
            const confirmElements = document.querySelectorAll('[data-confirm]');
            confirmElements.forEach(function(element) {
                element.addEventListener('click', function(e) {
                    const message = this.getAttribute('data-confirm');
                    if (!confirm(message)) {
                        e.preventDefault();
                        return false;
                    }
                });
            });
        });

        // Raccourcis clavier
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + R pour actualiser
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                window.location.reload();
            }
            
            // Échap pour revenir à la liste (si on est sur une page de détail)
            if (e.key === 'Escape' && window.location.pathname.includes('/show/')) {
                window.location.href = '{{ route("sms-dev.index") }}';
            }
        });
    </script>
</body>
</html>
