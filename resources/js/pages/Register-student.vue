<template>
    <div class="page-centered">
        <div class="register-card">
            <div class="header">
                <h2 class="title">Join as Applicant</h2>
                <p class="subtitle">Start your journey to the top tech companies</p>
            </div>

            <form class="register-form">
                <div class="form-group">
                    <label for="name" class="field-label">Full Name</label>
                    <input 
                        id="name"
                        type="text" 
                        placeholder="John Doe" 
                        class="form-input"
                    />
                </div>

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
                    Register
                </button>

                <p class="footer-text">
                    Already have an account? 
                    <router-link to="/login" class="login-link">Sign In</router-link>
                </p>
            </form>
        </div>
    </div>
</template>

<script setup>
</script>

<style scoped>
</style>