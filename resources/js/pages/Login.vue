<script setup>
import { reactive } from "vue";
import axios from "axios";

const form = reactive({
    email: "",
    password: "",
});

async function login() {
    try {
        const response = await axios.post(
            "http://127.0.0.1:8000/api/v1/login",
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
            <div class="header">
                <h2 class="cta-title">Welcome Back</h2>
                <p class="cta-subtitle">Enter your credentials to access your account</p>
            </div>

            <form @submit.prevent="login" class="register-form">
                <div class="form-group">
                    <label for="email" class="field-label">Email address</label>
                    <input 
                        id="email"
                        type="email" 
                        placeholder="john@example.com" 
                        v-model="form.email"
                        class="form-input"
                    />
                </div>

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

<style scoped>
</style>