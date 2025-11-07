<template>
    <div class="permissions-page">
        <div class="page-header">
            <h2>Permission Management</h2>
            <button @click="showCreateModal = true" class="btn btn-primary">
                <span>➕</span> Add Permission
            </button>
        </div>
        
        <div class="permissions-table-container">
            <table class="permissions-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Guard</th>
                        <th>Roles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="4" class="text-center">Loading...</td>
                    </tr>
                    <tr v-else-if="permissions.length === 0">
                        <td colspan="4" class="text-center">No permissions found</td>
                    </tr>
                    <tr v-else v-for="permission in permissions" :key="permission.id">
                        <td>{{ permission.name }}</td>
                        <td>{{ permission.guard_name }}</td>
                        <td>
                            <span v-for="(role, index) in permission.roles" :key="role.id" class="badge">
                                {{ role.name }}
                            </span>
                            <span v-if="!permission.roles || permission.roles.length === 0" class="text-muted">No roles</span>
                        </td>
                        <td>
                            <button @click="editPermission(permission)" class="btn btn-sm btn-edit">Edit</button>
                            <button @click="syncRoles(permission)" class="btn btn-sm btn-sync">Sync Roles</button>
                            <button @click="syncToUsers(permission)" class="btn btn-sm btn-sync-users">Sync to Users</button>
                            <button @click="handleDeletePermission(permission.id)" class="btn btn-sm btn-delete">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div v-if="pagination" class="pagination">
            <button
                @click="fetchPermissions({ page: pagination.current_page - 1 })"
                :disabled="pagination.current_page === 1"
                class="btn btn-sm"
            >
                Previous
            </button>
            <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
            <button
                @click="fetchPermissions({ page: pagination.current_page + 1 })"
                :disabled="pagination.current_page === pagination.last_page"
                class="btn btn-sm"
            >
                Next
            </button>
        </div>
        
        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal || editingPermission" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>{{ editingPermission ? 'Edit Permission' : 'Create Permission' }}</h3>
                    <button @click="closeModal" class="btn-close">×</button>
                </div>
                
                <form @submit.prevent="handleSubmit" class="modal-form">
                    <div class="form-group">
                        <label>Name</label>
                        <input v-model="form.name" type="text" class="form-control" placeholder="api.example.store" required />
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
        
        <!-- Sync Roles Modal -->
        <div v-if="syncingPermission" class="modal-overlay" @click="closeSyncModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Sync Roles to Permission: {{ syncingPermission.name }}</h3>
                    <button @click="closeSyncModal" class="btn-close">×</button>
                </div>
                
                <form @submit.prevent="handleSyncRoles" class="modal-form">
                    <div class="form-group">
                        <label>Select Roles</label>
                        <div class="roles-list">
                            <label v-for="role in allRoles" :key="role.id" class="role-item">
                                <input
                                    type="checkbox"
                                    :value="role.id"
                                    v-model="selectedRoles"
                                />
                                <span>{{ role.name }}</span>
                            </label>
                        </div>
                    </div>
                    
                    <div v-if="syncError" class="alert alert-error">{{ syncError }}</div>
                    <div v-if="syncSuccess" class="alert alert-success">{{ syncSuccess }}</div>
                    
                    <div class="modal-footer">
                        <button type="button" @click="closeSyncModal" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="syncSubmitting">
                            {{ syncSubmitting ? 'Syncing...' : 'Sync Roles' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Sync to Users Modal -->
        <div v-if="syncingToUsersPermission" class="modal-overlay" @click="closeSyncUsersModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3>Sync Permission to Users: {{ syncingToUsersPermission.name }}</h3>
                    <button @click="closeSyncUsersModal" class="btn-close">×</button>
                </div>
                
                <form @submit.prevent="handleSyncToUsers" class="modal-form">
                    <div class="form-group">
                        <label>Select Users</label>
                        <div class="users-list">
                            <label v-for="user in allUsers" :key="user.id" class="user-item">
                                <input
                                    type="checkbox"
                                    :value="user.id"
                                    v-model="selectedUsers"
                                />
                                <span>{{ user.name }} ({{ user.email }})</span>
                            </label>
                        </div>
                    </div>
                    
                    <div v-if="syncUsersError" class="alert alert-error">{{ syncUsersError }}</div>
                    <div v-if="syncUsersSuccess" class="alert alert-success">{{ syncUsersSuccess }}</div>
                    
                    <div class="modal-footer">
                        <button type="button" @click="closeSyncUsersModal" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="syncUsersSubmitting">
                            {{ syncUsersSubmitting ? 'Syncing...' : 'Sync to Users' }}
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
    name: 'Permissions',
    data() {
        return {
            showCreateModal: false,
            editingPermission: null,
            syncingPermission: null,
            syncingToUsersPermission: null,
            form: {
                name: '',
            },
            allRoles: [],
            allUsers: [],
            selectedRoles: [],
            selectedUsers: [],
            submitting: false,
            syncSubmitting: false,
            syncUsersSubmitting: false,
            error: null,
            success: null,
            syncError: null,
            syncSuccess: null,
            syncUsersError: null,
            syncUsersSuccess: null,
        };
    },
    computed: {
        ...mapGetters('permissions', ['permissions', 'pagination', 'loading']),
    },
    mounted() {
        this.fetchPermissions();
        this.fetchAllRoles();
        this.fetchAllUsers();
    },
    methods: {
        ...mapActions('permissions', ['fetchPermissions', 'createPermission', 'updatePermission', 'deletePermission', 'resyncRoles']),
        
        async fetchAllRoles() {
            try {
                const response = await api.get('/api/hierarchy/roles', { params: { per_page: 1000 } });
                this.allRoles = response.data.data || [];
            } catch (error) {
                console.error('Failed to fetch roles:', error);
            }
        },
        
        async fetchAllUsers() {
            try {
                const response = await api.get('/api/v1/user-management/users', { params: { per_page: 1000 } });
                this.allUsers = response.data.data || [];
            } catch (error) {
                console.error('Failed to fetch users:', error);
            }
        },
        
        editPermission(permission) {
            this.editingPermission = permission;
            this.form = {
                name: permission.name,
            };
        },
        
        syncRoles(permission) {
            this.syncingPermission = permission;
            this.selectedRoles = permission.roles?.map((r) => r.id) || [];
        },
        
        syncToUsers(permission) {
            this.syncingToUsersPermission = permission;
            this.selectedUsers = [];
        },
        
        closeModal() {
            this.showCreateModal = false;
            this.editingPermission = null;
            this.form = {
                name: '',
            };
            this.error = null;
            this.success = null;
        },
        
        closeSyncModal() {
            this.syncingPermission = null;
            this.selectedRoles = [];
            this.syncError = null;
            this.syncSuccess = null;
        },
        
        closeSyncUsersModal() {
            this.syncingToUsersPermission = null;
            this.selectedUsers = [];
            this.syncUsersError = null;
            this.syncUsersSuccess = null;
        },
        
        async handleSubmit() {
            this.submitting = true;
            this.error = null;
            this.success = null;
            
            let result;
            if (this.editingPermission) {
                result = await this.updatePermission({ id: this.editingPermission.id, ...this.form });
            } else {
                result = await this.createPermission(this.form);
            }
            
            if (result.success) {
                this.success = this.editingPermission ? 'Permission updated successfully!' : 'Permission created successfully!';
                this.fetchPermissions();
                setTimeout(() => {
                    this.closeModal();
                }, 1500);
            } else {
                this.error = result.error;
            }
            
            this.submitting = false;
        },
        
        async handleSyncRoles() {
            this.syncSubmitting = true;
            this.syncError = null;
            this.syncSuccess = null;
            
            const result = await this.resyncRoles({
                permissionId: this.syncingPermission.id,
                roles: this.selectedRoles,
            });
            
            if (result.success) {
                this.syncSuccess = 'Roles synced successfully!';
                this.fetchPermissions();
                setTimeout(() => {
                    this.closeSyncModal();
                }, 1500);
            } else {
                this.syncError = result.error;
            }
            
            this.syncSubmitting = false;
        },
        
        async handleSyncToUsers() {
            this.syncUsersSubmitting = true;
            this.syncUsersError = null;
            this.syncUsersSuccess = null;
            
            try {
                await api.post(`/api/hierarchy/permissions/resync/${this.syncingToUsersPermission.id}/to-user`, {
                    users: this.selectedUsers,
                });
                this.syncUsersSuccess = 'Permission synced to users successfully!';
                this.fetchPermissions();
                setTimeout(() => {
                    this.closeSyncUsersModal();
                }, 1500);
            } catch (error) {
                this.syncUsersError = error.response?.data?.message || 'Failed to sync permission to users';
            } finally {
                this.syncUsersSubmitting = false;
            }
        },
        
        async handleDeletePermission(permissionId) {
            if (confirm('Are you sure you want to delete this permission?')) {
                const result = await this.deletePermission(permissionId);
                if (result.success) {
                    this.fetchPermissions();
                } else {
                    alert(result.error);
                }
            }
        },
    },
};
</script>

<style scoped>
.permissions-page {
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

.permissions-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.permissions-table {
    width: 100%;
    border-collapse: collapse;
}

.permissions-table thead {
    background: #f8f9fa;
}

.permissions-table th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.permissions-table td {
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

.btn-sync-users {
    background: #f59e0b;
    color: white;
}

.btn-sync-users:hover {
    background: #d97706;
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

.roles-list,
.users-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 12px;
}

.role-item,
.user-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.role-item:hover,
.user-item:hover {
    background-color: #f8f9fa;
}

.role-item input[type="checkbox"],
.user-item input[type="checkbox"] {
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

