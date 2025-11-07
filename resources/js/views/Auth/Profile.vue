<template>
    <div class="profile">
        <div class="profile-header">
            <h2>Profile</h2>
            <p>Manage your profile information</p>
        </div>
        
        <div class="profile-content">
            <div class="profile-card">
                <h3>Personal Information</h3>
                
                <form @submit.prevent="handleUpdateProfile" class="profile-form">
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
                    
                    <div v-if="error" class="alert alert-error">
                        {{ error }}
                    </div>
                    
                    <div v-if="success" class="alert alert-success">
                        {{ success }}
                    </div>
                    
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span v-if="loading">Updating...</span>
                        <span v-else>Update Profile</span>
                    </button>
                </form>
            </div>
            
            <div class="profile-card">
                <h3>Change Password</h3>
                
                <form @submit.prevent="handleChangePassword" class="profile-form">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input
                            id="password"
                            v-model="passwordForm.password"
                            type="password"
                            class="form-control"
                            placeholder="Enter new password"
                            required
                            minlength="8"
                        />
                    </div>
                    
                    <div v-if="passwordError" class="alert alert-error">
                        {{ passwordError }}
                    </div>
                    
                    <div v-if="passwordSuccess" class="alert alert-success">
                        {{ passwordSuccess }}
                    </div>
                    
                    <button type="submit" class="btn btn-primary" :disabled="passwordLoading">
                        <span v-if="passwordLoading">Changing...</span>
                        <span v-else>Change Password</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
    name: 'Profile',
    data() {
        return {
            form: {
                name: '',
                email: '',
            },
            passwordForm: {
                password: '',
            },
            loading: false,
            passwordLoading: false,
            error: null,
            success: null,
            passwordError: null,
            passwordSuccess: null,
        };
    },
    computed: {
        ...mapGetters('auth', ['user']),
    },
    mounted() {
        if (this.user) {
            this.form.name = this.user.name || '';
            this.form.email = this.user.email || '';
        } else {
            this.$store.dispatch('auth/fetchProfile').then((result) => {
                if (result.success) {
                    this.form.name = result.data.name || '';
                    this.form.email = result.data.email || '';
                }
            });
        }
    },
    methods: {
        ...mapActions('auth', ['updateProfile', 'changePassword']),
        
        async handleUpdateProfile() {
            this.loading = true;
            this.error = null;
            this.success = null;
            
            const result = await this.updateProfile(this.form);
            
            if (result.success) {
                this.success = 'Profile updated successfully!';
                setTimeout(() => {
                    this.success = null;
                }, 3000);
            } else {
                this.error = result.error;
            }
            
            this.loading = false;
        },
        
        async handleChangePassword() {
            this.passwordLoading = true;
            this.passwordError = null;
            this.passwordSuccess = null;
            
            const result = await this.changePassword(this.passwordForm);
            
            if (result.success) {
                this.passwordSuccess = 'Password changed successfully!';
                this.passwordForm.password = '';
                setTimeout(() => {
                    this.passwordSuccess = null;
                }, 3000);
            } else {
                this.passwordError = result.error;
            }
            
            this.passwordLoading = false;
        },
    },
};
</script>

<style scoped>
.profile {
    max-width: 800px;
    margin: 0 auto;
}

.profile-header {
    margin-bottom: 32px;
}

.profile-header h2 {
    font-size: 28px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.profile-header p {
    color: #666;
    font-size: 14px;
}

.profile-content {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.profile-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.profile-card h3 {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin-bottom: 24px;
}

.profile-form {
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
    align-self: flex-start;
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
</style>

