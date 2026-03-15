<template>
    <div class="splash-container">
        <!-- Topbar navigation (minimalist) -->
        <header class="topbar">
            <div class="topbar-left">
                <div class="logo">
                    <span class="logo-leet-blue">LEET</span>
                    <span class="logo-match">MATCH</span>
                </div>
            </div>
            <div class="topbar-right">
                <button class="btn-outline-sm">Contact</button>
                <button class="btn-primary-sm">Demo</button>
            </div>
        </header>

        <!-- Main splash content with sidebar structure -->
        <div class="splash-layout">
            <!-- Sidebar navigation (minimalist) -->
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <a href="#" class="nav-item active">
                        <svg
                            class="icon"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                            />
                        </svg>
                        <span>Accueil</span>
                    </a>
                    <a href="#" class="nav-item">
                        <svg
                            class="icon"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                        <span>Talents</span>
                    </a>
                    <a href="#" class="nav-item">
                        <svg
                            class="icon"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                            />
                        </svg>
                        <span>Entreprises</span>
                    </a>
                    <a href="#" class="nav-item">
                        <svg
                            class="icon"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                            />
                        </svg>
                        <span>Tableau de bord</span>
                    </a>
                </nav>
                <div class="sidebar-footer">
                    <div class="badge">Beta</div>
                </div>
            </aside>

            <!-- Main content area -->
            <main class="main-content">
                <!-- Welcome section with visual indicators -->
                <div class="welcome-header">
                    <h1 class="welcome-title">Bienvenue sur LeetMatch</h1>
                    <p class="welcome-subtitle">
                        La plateforme qui connecte les talents CS aux meilleures
                        entreprises
                    </p>
                </div>

                <!-- Dashboard indicators (progress bars, stats) -->
                <div class="indicators-grid">
                    <div class="indicator-card">
                        <div class="card-header">
                            <h3>Talents inscrits</h3>
                            <span class="badge success">+12%</span>
                        </div>
                        <div class="stat-value">2,847</div>
                        <div class="progress-container">
                            <div class="progress-bar">
                                <div
                                    class="progress-fill"
                                    style="width: 75%"
                                ></div>
                            </div>
                            <span class="progress-label">Objectif: 75%</span>
                        </div>
                    </div>

                    <div class="indicator-card">
                        <div class="card-header">
                            <h3>Entreprises partenaires</h3>
                            <span class="badge success">+8%</span>
                        </div>
                        <div class="stat-value">156</div>
                        <div class="progress-container">
                            <div class="progress-bar">
                                <div
                                    class="progress-fill"
                                    style="width: 62%"
                                ></div>
                            </div>
                            <span class="progress-label"
                                >62% de croissance</span
                            >
                        </div>
                    </div>

                    <div class="indicator-card">
                        <div class="card-header">
                            <h3>Matchs réussis</h3>
                            <span class="badge warning">+24%</span>
                        </div>
                        <div class="stat-value">892</div>
                        <div class="progress-container">
                            <div class="progress-bar">
                                <div
                                    class="progress-fill"
                                    style="width: 89%"
                                ></div>
                            </div>
                            <span class="progress-label"
                                >Taux de satisfaction: 89%</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Alert example (user feedback) -->
                <div class="alert success">
                    <svg
                        class="alert-icon"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <span
                        >Nouveau: 15 talents CS viennent de rejoindre la
                        plateforme</span
                    >
                </div>

                <!-- Login/Signup card (minimalist style) -->
                <div class="auth-card">
                    <div class="auth-tabs">
                        <button class="tab active">Connexion</button>
                        <button class="tab">Inscription</button>
                    </div>

                    <form class="auth-form">
                        <div class="form-group">
                            <label>Email</label>
                            <input
                                type="email"
                                placeholder="vous@exemple.com"
                                class="form-input form-input-blue"
                            />
                        </div>
                        <div class="form-group">
                            <label>Mot de passe</label>
                            <input
                                type="password"
                                placeholder="••••••••"
                                class="form-input form-input-blue"
                            />
                        </div>

                        <div class="form-options">
                            <label class="checkbox">
                                <input type="checkbox" />
                                <span>Se souvenir de moi</span>
                            </label>
                            <a href="#" class="link">Mot de passe oublié?</a>
                        </div>

                        <button type="submit" class="btn-primary-blue">
                            Se connecter
                        </button>
                    </form>

                    <div class="auth-divider">
                        <span>ou continuer avec</span>
                    </div>

                    <div class="social-buttons">
                        <button class="btn-social">
                            <svg class="social-icon" viewBox="0 0 24 24">
                                <path
                                    fill="currentColor"
                                    d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.166 6.839 9.489.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.462-1.11-1.462-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.03-2.682-.103-.253-.447-1.27.098-2.646 0 0 .84-.269 2.75 1.025.8-.223 1.65-.334 2.5-.334.85 0 1.7.111 2.5.334 1.91-1.294 2.75-1.025 2.75-1.025.545 1.376.201 2.393.099 2.646.64.698 1.03 1.591 1.03 2.682 0 3.841-2.337 4.687-4.565 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.161 22 16.418 22 12c0-5.523-4.477-10-10-10z"
                                />
                            </svg>
                            GitHub
                        </button>
                        <button class="btn-social">
                            <svg class="social-icon" viewBox="0 0 24 24">
                                <path
                                    fill="currentColor"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                />
                                <path
                                    fill="currentColor"
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                />
                                <path
                                    fill="currentColor"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                />
                                <path
                                    fill="currentColor"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                />
                            </svg>
                            Google
                        </button>
                    </div>

                    <p class="auth-footer">
                        En vous inscrivant, vous acceptez nos
                        <a href="#" class="link">conditions d'utilisation</a>
                    </p>
                </div>

                <!-- Footer with badges -->
                <div class="footer-badges">
                    <span class="badge">Nouveau</span>
                    <span class="badge success">Match du jour</span>
                    <span class="badge warning">Tendances</span>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
// No script needed for static splash page
// Add your login/signup logic here later
</script>

<style scoped>
</style>

