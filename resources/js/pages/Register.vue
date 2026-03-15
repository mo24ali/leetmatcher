<template>
    <div class="page-centered">
        <div class="role-card">
            <h1 class="role-title">Join as</h1>
            
            <!-- Applicant link -->
            <router-link 
                to="/register-student" 
                class="role-link applicant"
            >
                <span class="role-label">Are you an applicant?</span>
                <span class="role-description">Find your next opportunity</span>
                <span class="arrow">→</span>
            </router-link>

            <!-- Recruiter link -->
            <router-link 
                to="/register-recruiter" 
                class="role-link recruiter"
            >
                <span class="role-label">Are you a recruiter?</span>
                <span class="role-description">Hire the best talent</span>
                <span class="arrow">→</span>
            </router-link>

            <!-- Subtle divider -->
            <div class="divider"></div>

            <!-- Optional footer text -->
            <p class="footer-text">
                Already have an account? 
                <router-link to="/login" class="login-link">Sign in</router-link>
            </p>
        </div>
    </div>
</template>

<style scoped>
</style>