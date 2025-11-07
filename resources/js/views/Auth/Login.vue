<template>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Login</h1>
                <p>Welcome back! Please login to your account.</p>
            </div>
            
            <form @submit.prevent="handleLogin" class="login-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="form-control"
                        placeholder="Enter your email"
                        required
                    />
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="form-control"
                        placeholder="Enter your password"
                        required
                    />
                </div>
                
                <div v-if="error" class="alert alert-error">
                    {{ error }}
                </div>
                
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <span v-if="loading">Logging in...</span>
                    <span v-else>Login</span>
                </button>
                
                <div class="login-footer">
                    <p>Don't have an account? <router-link to="/register">Register here</router-link></p>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
    name: 'Login',
    data() {
        return {
            form: {
                email: '',
                password: '',
            },
            loading: false,
            error: null,
        };
    },
    methods: {
        ...mapActions('auth', ['login']),
        
        async handleLogin() {
            this.loading = true;
            this.error = null;
            
            const result = await this.login(this.form);
            
            if (result.success) {
                this.$router.push({ name: 'Dashboard' });
            } else {
                this.error = result.error;
            }
            
            this.loading = false;
        },
    },
};
</script>

<style scoped>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
}

.login-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    padding: 40px;
    width: 100%;
    max-width: 400px;
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-header h1 {
    font-size: 28px;
    color: #333;
    margin-bottom: 8px;
}

.login-header p {
    color: #666;
    font-size: 14px;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.form-control {
    padding: 12px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
}

.alert-error {
    background-color: #fee;
    color: #c33;
    border: 1px solid #fcc;
}

.login-footer {
    text-align: center;
    margin-top: 20px;
}

.login-footer p {
    color: #666;
    font-size: 14px;
}

.login-footer a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.login-footer a:hover {
    text-decoration: underline;
}
</style>

