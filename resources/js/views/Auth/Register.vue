<template>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1>Register</h1>
                <p>Create a new account to get started.</p>
            </div>
            
            <form @submit.prevent="handleRegister" class="register-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="form-control"
                        placeholder="Enter your full name"
                        required
                    />
                </div>
                
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
                        minlength="8"
                    />
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="form-control"
                        placeholder="Confirm your password"
                        required
                        minlength="8"
                    />
                </div>
                
                <div v-if="error" class="alert alert-error">
                    <div v-if="Array.isArray(error)">
                        <ul style="margin: 0; padding-left: 20px;">
                            <li v-for="(err, index) in error" :key="index">{{ err }}</li>
                        </ul>
                    </div>
                    <div v-else>{{ error }}</div>
                </div>
                
                <div v-if="success" class="alert alert-success">
                    {{ success }}
                </div>
                
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <span v-if="loading">Registering...</span>
                    <span v-else>Register</span>
                </button>
                
                <div class="register-footer">
                    <p>Already have an account? <router-link to="/login">Login here</router-link></p>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
    name: 'Register',
    data() {
        return {
            form: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
            },
            loading: false,
            error: null,
            success: null,
        };
    },
    methods: {
        ...mapActions('auth', ['register']),
        
        async handleRegister() {
            this.loading = true;
            this.error = null;
            this.success = null;
            
            if (this.form.password !== this.form.password_confirmation) {
                this.error = 'Passwords do not match';
                this.loading = false;
                return;
            }
            
            const result = await this.register(this.form);
            
            if (result.success) {
                this.success = 'Registration successful! Please check your email for OTP verification.';
                setTimeout(() => {
                    this.$router.push({ name: 'Login' });
                }, 2000);
            } else {
                this.error = result.error;
            }
            
            this.loading = false;
        },
    },
};
</script>

<style scoped>
.register-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
}

.register-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    padding: 40px;
    width: 100%;
    max-width: 400px;
}

.register-header {
    text-align: center;
    margin-bottom: 30px;
}

.register-header h1 {
    font-size: 28px;
    color: #333;
    margin-bottom: 8px;
}

.register-header p {
    color: #666;
    font-size: 14px;
}

.register-form {
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

.alert-success {
    background-color: #efe;
    color: #3c3;
    border: 1px solid #cfc;
}

.register-footer {
    text-align: center;
    margin-top: 20px;
}

.register-footer p {
    color: #666;
    font-size: 14px;
}

.register-footer a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.register-footer a:hover {
    text-decoration: underline;
}
</style>

