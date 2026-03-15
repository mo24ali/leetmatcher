<template>
    <div class="role-selection">
        <div class="role-card">
            <h2 class="role-title">Join as</h2>
            
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
.role-selection {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background-color: var(--gray-50);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Main card */
.role-card {
    max-width: 480px;
    width: 100%;
    background-color: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 16px;
    padding: 3rem 2.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
}

/* Title */
.role-title {
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: -0.02em;
    color: var(--gray-900);
    margin-bottom: 2.5rem;
    text-align: center;
}

/* Role links */
.role-link {
    display: flex;
    flex-direction: column;
    padding: 1.5rem;
    margin-bottom: 1rem;
    text-decoration: none;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    transition: all 0.2s ease;
    position: relative;
}

.role-link:hover {
    border-color: var(--gray-400);
    background-color: var(--gray-50);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Applicant specific style */
.role-link.applicant {
    border-left: 3px solid var(--gray-900);
}

/* Recruiter specific style */
.role-link.recruiter {
    border-left: 3px solid var(--gray-400);
}

/* Role label */
.role-label {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

/* Role description */
.role-description {
    font-size: 0.95rem;
    color: var(--gray-500);
    line-height: 1.5;
}

/* Arrow indicator */
.arrow {
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.5rem;
    color: var(--gray-400);
    transition: transform 0.2s ease;
}

.role-link:hover .arrow {
    transform: translate(5px, -50%);
    color: var(--gray-900);
}

/* Divider */
.divider {
    height: 1px;
    background-color: var(--gray-200);
    margin: 2rem 0 1.5rem;
}

/* Footer text */
.footer-text {
    text-align: center;
    font-size: 0.9rem;
    color: var(--gray-500);
    margin: 0;
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
    .role-selection {
        padding: 1rem;
    }

    .role-card {
        padding: 2rem 1.5rem;
    }

    .role-title {
        font-size: 1.75rem;
        margin-bottom: 2rem;
    }

    .role-link {
        padding: 1.25rem;
    }

    .role-label {
        font-size: 1.1rem;
    }

    .role-description {
        font-size: 0.85rem;
    }

    .arrow {
        font-size: 1.25rem;
        right: 1.25rem;
    }
}

@media (max-width: 480px) {
    .role-card {
        padding: 1.5rem 1rem;
    }

    .role-title {
        font-size: 1.5rem;
    }
}

/* Font import */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
</style>