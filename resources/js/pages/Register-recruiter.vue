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
    <div class="page-centered">
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
</style>