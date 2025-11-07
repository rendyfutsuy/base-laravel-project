<template>
    <div class="superadmins-page">
        <div class="page-header">
            <h2>Superadmin Management</h2>
            <button @click="showCreateModal = true" class="btn btn-primary">
                <span>➕</span> Add Superadmin
            </button>
        </div>
        
        <div class="superadmins-table-container">
            <table class="superadmins-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="4" class="text-center">Loading...</td>
                    </tr>
                    <tr v-else-if="superadmins.length === 0">
                        <td colspan="4" class="text-center">No superadmins found</td>
                    </tr>
                    <tr v-else v-for="superadmin in superadmins" :key="superadmin.id">
                        <td>{{ superadmin.name }}</td>
                        <td>{{ superadmin.email }}</td>
                        <td>
                            <span v-for="(role, index) in superadmin.roles" :key="role.id" class="badge">
                                {{ role.name }}
                            </span>
                            <span v-if="!superadmin.roles || superadmin.roles.length === 0" class="text-muted">No roles</span>
                        </td>
                        <td>
                            <button @click="editSuperadmin(superadmin)" class="btn btn-sm btn-edit">Edit</button>
                            <button @click="deleteSuperadmin(superadmin.id)" class="btn btn-sm btn-delete">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div v-if="pagination" class="pagination">
            <button
                @click="fetchSuperadmins({ page: pagination.current_page - 1 })"
                :disabled="pagination.current_page === 1"
                class="btn btn-sm"
            >
                Previous
            </button>
            <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
            <button
                @click="fetchSuperadmins({ page: pagination.current_page + 1 })"
                :disabled="pagination.current_page === pagination.last_page"
                class="btn btn-sm"
            >
                Next
            </button>
        </div>
        
        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal || editingSuperadmin" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>{{ editingSuperadmin ? 'Edit Superadmin' : 'Create Superadmin' }}</h3>
                    <button @click="closeModal" class="btn-close">×</button>
                </div>
                
                <form @submit.prevent="handleSubmit" class="modal-form">
                    <div class="form-group">
                        <label>Name</label>
                        <input v-model="form.name" type="text" class="form-control" required />
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input v-model="form.email" type="email" class="form-control" required />
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input v-model="form.password" type="password" class="form-control" :required="!editingSuperadmin" />
                    </div>
                    
                    <div v-if="error" class="alert alert-error">{{ error }}</div>
                    <div v-if="success" class="alert alert-success">{{ success }}</div>
                    
                    <div class="modal-footer">
                        <button type="button" @click="closeModal" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="submitting">
                            {{ submitting ? 'Saving...' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import api from '../../services/api';

export default {
    name: 'Superadmins',
    data() {
        return {
            superadmins: [],
            pagination: null,
            loading: false,
            showCreateModal: false,
            editingSuperadmin: null,
            form: {
                name: '',
                email: '',
                password: '',
            },
            submitting: false,
            error: null,
            success: null,
        };
    },
    mounted() {
        this.fetchSuperadmins();
    },
    methods: {
        async fetchSuperadmins(params = {}) {
            this.loading = true;
            try {
                const response = await api.get('/api/v1/user-management/superadmins', { params });
                this.superadmins = response.data.data || [];
                this.pagination = {
                    current_page: response.data.meta?.current_page || 1,
                    last_page: response.data.meta?.last_page || 1,
                    per_page: response.data.meta?.per_page || 10,
                    total: response.data.meta?.total || 0,
                };
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch superadmins';
            } finally {
                this.loading = false;
            }
        },
        
        editSuperadmin(superadmin) {
            this.editingSuperadmin = superadmin;
            this.form = {
                name: superadmin.name,
                email: superadmin.email,
                password: '',
            };
        },
        
        closeModal() {
            this.showCreateModal = false;
            this.editingSuperadmin = null;
            this.form = {
                name: '',
                email: '',
                password: '',
            };
            this.error = null;
            this.success = null;
        },
        
        async handleSubmit() {
            this.submitting = true;
            this.error = null;
            this.success = null;
            
            try {
                const userData = { ...this.form };
                if (!userData.password) {
                    delete userData.password;
                }
                
                let response;
                if (this.editingSuperadmin) {
                    response = await api.put(`/api/v1/user-management/superadmins/${this.editingSuperadmin.id}`, userData);
                } else {
                    response = await api.post('/api/v1/user-management/superadmins', userData);
                }
                
                this.success = this.editingSuperadmin ? 'Superadmin updated successfully!' : 'Superadmin created successfully!';
                this.fetchSuperadmins();
                setTimeout(() => {
                    this.closeModal();
                }, 1500);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to save superadmin';
            } finally {
                this.submitting = false;
            }
        },
        
        async deleteSuperadmin(superadminId) {
            if (confirm('Are you sure you want to delete this superadmin?')) {
                try {
                    await api.delete(`/api/v1/user-management/superadmins/${superadminId}`);
                    this.fetchSuperadmins();
                } catch (error) {
                    alert(error.response?.data?.message || 'Failed to delete superadmin');
                }
            }
        },
    },
};
</script>

<style scoped>
.superadmins-page {
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.page-header h2 {
    font-size: 24px;
    font-weight: 600;
    color: #333;
}

.superadmins-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.superadmins-table {
    width: 100%;
    border-collapse: collapse;
}

.superadmins-table thead {
    background: #f8f9fa;
}

.superadmins-table th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.superadmins-table td {
    padding: 16px;
    border-top: 1px solid #eee;
    font-size: 14px;
    color: #666;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    background: #667eea;
    color: white;
    border-radius: 4px;
    font-size: 12px;
    margin-right: 4px;
}

.text-muted {
    color: #999;
    font-size: 12px;
}

.text-center {
    text-align: center;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-edit {
    background: #667eea;
    color: white;
    margin-right: 8px;
}

.btn-edit:hover {
    background: #5568d3;
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
}

.btn-secondary {
    background: #e5e7eb;
    color: #333;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    margin-top: 24px;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid #eee;
}

.modal-header h3 {
    font-size: 20px;
    font-weight: 600;
    color: #333;
}

.btn-close {
    background: none;
    border: none;
    font-size: 32px;
    color: #999;
    cursor: pointer;
    line-height: 1;
}

.btn-close:hover {
    color: #333;
}

.modal-form {
    padding: 24px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.form-control {
    width: 100%;
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

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 16px;
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

