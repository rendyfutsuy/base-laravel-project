<template>
    <div class="roles-page">
        <div class="page-header">
            <h2>Role Management</h2>
            <button @click="showCreateModal = true" class="btn btn-primary">
                <span>➕</span> Add Role
            </button>
        </div>
        
        <div class="roles-table-container">
            <table class="roles-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Guard</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="4" class="text-center">Loading...</td>
                    </tr>
                    <tr v-else-if="roles.length === 0">
                        <td colspan="4" class="text-center">No roles found</td>
                    </tr>
                    <tr v-else v-for="role in roles" :key="role.id">
                        <td>{{ role.name }}</td>
                        <td>{{ role.guard_name }}</td>
                        <td>
                            <span v-for="(permission, index) in role.permissions" :key="permission.id" class="badge">
                                {{ permission.name }}
                            </span>
                            <span v-if="!role.permissions || role.permissions.length === 0" class="text-muted">No permissions</span>
                        </td>
                        <td>
                            <button @click="editRole(role)" class="btn btn-sm btn-edit">Edit</button>
                            <button @click="syncPermissions(role)" class="btn btn-sm btn-sync">Sync Permissions</button>
                            <button @click="handleDeleteRole(role.id)" class="btn btn-sm btn-delete">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div v-if="pagination" class="pagination">
            <button
                @click="fetchRoles({ page: pagination.current_page - 1 })"
                :disabled="pagination.current_page === 1"
                class="btn btn-sm"
            >
                Previous
            </button>
            <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
            <button
                @click="fetchRoles({ page: pagination.current_page + 1 })"
                :disabled="pagination.current_page === pagination.last_page"
                class="btn btn-sm"
            >
                Next
            </button>
        </div>
        
        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal || editingRole" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>{{ editingRole ? 'Edit Role' : 'Create Role' }}</h3>
                    <button @click="closeModal" class="btn-close">×</button>
                </div>
                
                <form @submit.prevent="handleSubmit" class="modal-form">
                    <div class="form-group">
                        <label>Name</label>
                        <input v-model="form.name" type="text" class="form-control" required />
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
        
        <!-- Sync Permissions Modal -->
        <div v-if="syncingRole" class="modal-overlay" @click="closeSyncModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Sync Permissions to Role: {{ syncingRole.name }}</h3>
                    <button @click="closeSyncModal" class="btn-close">×</button>
                </div>
                
                <form @submit.prevent="handleSyncPermissions" class="modal-form">
                    <div class="form-group">
                        <label>Select Permissions</label>
                        <div class="permissions-list">
                            <label v-for="permission in allPermissions" :key="permission.id" class="permission-item">
                                <input
                                    type="checkbox"
                                    :value="permission.id"
                                    v-model="selectedPermissions"
                                />
                                <span>{{ permission.name }}</span>
                            </label>
                        </div>
                    </div>
                    
                    <div v-if="syncError" class="alert alert-error">{{ syncError }}</div>
                    <div v-if="syncSuccess" class="alert alert-success">{{ syncSuccess }}</div>
                    
                    <div class="modal-footer">
                        <button type="button" @click="closeSyncModal" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="syncSubmitting">
                            {{ syncSubmitting ? 'Syncing...' : 'Sync Permissions' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import api from '../../services/api';

export default {
    name: 'Roles',
    data() {
        return {
            showCreateModal: false,
            editingRole: null,
            syncingRole: null,
            form: {
                name: '',
            },
            allPermissions: [],
            selectedPermissions: [],
            submitting: false,
            syncSubmitting: false,
            error: null,
            success: null,
            syncError: null,
            syncSuccess: null,
        };
    },
    computed: {
        ...mapGetters('roles', ['roles', 'pagination', 'loading']),
    },
    mounted() {
        this.fetchRoles();
        this.fetchAllPermissions();
    },
    methods: {
        ...mapActions('roles', ['fetchRoles', 'createRole', 'updateRole', 'deleteRole', 'syncPermissionsToRole']),
        
        async fetchAllPermissions() {
            try {
                const response = await api.get('/api/v1/hierarchy/permissions', { params: { per_page: 1000 } });
                const responseData = response.data;
                // Handle different response structures
                this.allPermissions = responseData?.data || responseData || [];
            } catch (error) {
                console.error('Failed to fetch permissions:', error);
            }
        },
        
        editRole(role) {
            this.editingRole = role;
            this.form = {
                name: role.name,
            };
        },
        
        syncPermissions(role) {
            this.syncingRole = role;
            this.selectedPermissions = role.permissions?.map((p) => p.id) || [];
        },
        
        closeModal() {
            this.showCreateModal = false;
            this.editingRole = null;
            this.form = {
                name: '',
            };
            this.error = null;
            this.success = null;
        },
        
        closeSyncModal() {
            this.syncingRole = null;
            this.selectedPermissions = [];
            this.syncError = null;
            this.syncSuccess = null;
        },
        
        async handleSubmit() {
            this.submitting = true;
            this.error = null;
            this.success = null;
            
            let result;
            if (this.editingRole) {
                result = await this.updateRole({ id: this.editingRole.id, ...this.form });
            } else {
                result = await this.createRole(this.form);
            }
            
            if (result.success) {
                this.success = this.editingRole ? 'Role updated successfully!' : 'Role created successfully!';
                this.fetchRoles();
                setTimeout(() => {
                    this.closeModal();
                }, 1500);
            } else {
                this.error = result.error;
            }
            
            this.submitting = false;
        },
        
        async handleSyncPermissions() {
            this.syncSubmitting = true;
            this.syncError = null;
            this.syncSuccess = null;
            
            const result = await this.syncPermissionsToRole({
                roleId: this.syncingRole.id,
                permissionIds: this.selectedPermissions,
            });
            
            if (result.success) {
                this.syncSuccess = 'Permissions synced successfully!';
                this.fetchRoles();
                setTimeout(() => {
                    this.closeSyncModal();
                }, 1500);
            } else {
                this.syncError = result.error;
            }
            
            this.syncSubmitting = false;
        },
        
        async handleDeleteRole(roleId) {
            if (confirm('Are you sure you want to delete this role?')) {
                const result = await this.deleteRole(roleId);
                if (result.success) {
                    this.fetchRoles();
                } else {
                    alert(result.error);
                }
            }
        },
    },
};
</script>

<style scoped>
.roles-page {
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

.roles-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.roles-table {
    width: 100%;
    border-collapse: collapse;
}

.roles-table thead {
    background: #f8f9fa;
}

.roles-table th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.roles-table td {
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
    margin-bottom: 4px;
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
    margin-right: 8px;
}

.btn-edit {
    background: #667eea;
    color: white;
}

.btn-edit:hover {
    background: #5568d3;
}

.btn-sync {
    background: #10b981;
    color: white;
}

.btn-sync:hover {
    background: #059669;
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
    max-width: 600px;
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

.permissions-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 12px;
}

.permission-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.permission-item:hover {
    background-color: #f8f9fa;
}

.permission-item input[type="checkbox"] {
    cursor: pointer;
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

