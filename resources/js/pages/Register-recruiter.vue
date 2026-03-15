<script setup>
import { reactive } from "vue";
import axios from "axios";

const form = reactive({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

async function register() {
    try {
        const response = await axios.post(
            "http://127.0.0.1:8000/api/v1/register",
            form,
        );

        console.log(response.data);
    } catch (error) {
        console.log(error.response.data);
    }
}
</script>

<template>
    <div class="register-container">
        <div class="register-card">
            <!-- Header -->
            <div class="header">
                <h2 class="title">Create account</h2>
                <p class="subtitle">Join as a recruiter</p>
            </div>

            <!-- Registration form -->
            <form @submit.prevent="register" class="register-form">
                <!-- Name field -->
                <div class="form-group">
                    <label for="name" class="field-label">Full name</label>
                    <input 
                        id="name"
                        type="text" 
                        placeholder="John Doe" 
                        v-model="form.name" 
                        class="form-input"
                    />
                </div>

                <!-- Email field -->
                <div class="form-group">
                    <label for="email" class="field-label">Email address</label>
                    <input 
                        id="email"
                        type="email" 
                        placeholder="john@company.com" 
                        v-model="form.email" 
                        class="form-input"
                    />
                </div>

                <!-- Password field -->
                <div class="form-group">
                    <label for="password" class="field-label">Password</label>
                    <input 
                        id="password"
                        type="password" 
                        placeholder="••••••••" 
                        v-model="form.password" 
                        class="form-input"
                    />
                </div>

                <!-- Confirm password field -->
                <div class="form-group">
                    <label for="password_confirmation" class="field-label">Confirm password</label>
                    <input 
                        id="password_confirmation"
                        type="password" 
                        placeholder="••••••••" 
                        v-model="form.password_confirmation" 
                        class="form-input"
                    />
                </div>

                <!-- Submit button -->
                <button type="submit" class="submit-btn">
                    Register
                </button>

                <!-- Login link -->
                <p class="footer-text">
                    Already have an account? 
                    <router-link to="/login" class="login-link">Sign in</router-link>
                </p>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Minimalist monochrome variables */
:root {
    --black: #000000;
    --white: #ffffff;
    --gray-50: #fafafa;
    --gray-100: #f5f5f5;
    --gray-200: #e5e5e5;
    --gray-300: #d4d4d4;
    --gray-400: #a3a3a3;
    --gray-500: #737373;
    --gray-600: #525252;
    --gray-700: #404040;
    --gray-800: #262626;
    --gray-900: #171717;
}

/* Container */
.register-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background-color: var(--gray-50);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Card */
.register-card {
    max-width: 480px;
    width: 100%;
    background-color: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 20px;
    padding: 3rem 2.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
}

/* Header */
.header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.title {
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: -0.02em;
    color: var(--gray-900);
    margin-bottom: 0.5rem;
}

.subtitle {
    font-size: 0.95rem;
    color: var(--gray-500);
    font-weight: 400;
    letter-spacing: -0.01em;
}

/* Form */
.register-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Form group */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

/* Field label */
.field-label {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

/* Input field */
.form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    font-size: 0.95rem;
    font-family: 'Inter', sans-serif;
    color: var(--gray-900);
    background-color: var(--white);
    transition: all 0.2s ease;
}

.form-input::placeholder {
    color: var(--gray-400);
    font-weight: 400;
}

.form-input:focus {
    outline: none;
    border-color: var(--gray-900);
    background-color: var(--white);
    box-shadow: 0 0 0 3px var(--gray-100);
}

/* Submit button */
.submit-btn {
    width: 100%;
    padding: 1rem;
    margin-top: 1rem;
    background-color: var(--gray-900);
    color: var(--white);
    border: 1px solid var(--gray-900);
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    letter-spacing: 0.02em;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Inter', sans-serif;
}

.submit-btn:hover {
    background-color: var(--gray-800);
    border-color: var(--gray-800);
    transform: translateY(-1px);
}

.submit-btn:active {
    transform: translateY(0);
}

/* Footer text */
.footer-text {
    text-align: center;
    font-size: 0.9rem;
    color: var(--gray-500);
    margin: 1.5rem 0 0;
}

/* Login link */
.login-link {
    color: var(--gray-900);
    text-decoration: none;
    font-weight: 500;
    margin-left: 0.25rem;
    border-bottom: 1px solid transparent;
    transition: border-color 0.2s ease;
}

.login-link:hover {
    border-bottom-color: var(--gray-900);
}

/* Responsive design */
@media (max-width: 640px) {
    .register-container {
        padding: 1rem;
    }

    .register-card {
        padding: 2rem 1.5rem;
    }

    .title {
        font-size: 1.75rem;
    }

    .subtitle {
        font-size: 0.9rem;
    }

    .form-input {
        padding: 0.75rem 1rem;
    }
}

@media (max-width: 480px) {
    .register-card {
        padding: 1.5rem 1rem;
    }

    .title {
        font-size: 1.5rem;
    }

    .field-label {
        font-size: 0.8rem;
    }
}

/* Font import */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
</style>