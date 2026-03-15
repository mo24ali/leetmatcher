<template>
    <div class="page-centered">
        <div class="register-card">
            <div class="header">
                <h2 class="title">Welcome Back</h2>
                <p class="subtitle">Enter your credentials to access your account</p>
            </div>

            <form class="register-form">
                <div class="form-group">
                    <label for="email" class="field-label">Email address</label>
                    <input 
                        id="email"
                        type="email" 
                        placeholder="john@example.com" 
                        class="form-input"
                    />
                </div>

                <div class="form-group">
                    <label for="password" class="field-label">Password</label>
                    <input 
                        id="password"
                        type="password" 
                        placeholder="••••••••" 
                        class="form-input"
                    />
                </div>

                <button type="submit" class="submit-btn">
                    Sign In
                </button>

                <p class="footer-text">
                    Don't have an account? 
                    <router-link to="/register" class="login-link">Register</router-link>
                </p>
            </form>
        </div>
    </div>
</template>

<script setup>
</script>

<style scoped>
</style>