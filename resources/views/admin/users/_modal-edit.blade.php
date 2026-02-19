<!-- Modal para Editar Usuario -->
<x-modal id="editUserModal" title="Editar Usuario" size="md">
    <form id="editUserForm" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-4">
            <x-input 
                label="Nombre Completo" 
                name="name" 
                id="edit_name"
                required 
            />

            <x-input 
                label="Correo Electrónico" 
                name="email" 
                type="email" 
                id="edit_email"
                required 
            />

            <x-input 
                label="Teléfono" 
                name="phone" 
                id="edit_phone"
            />

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                <textarea 
                    name="address" 
                    id="edit_address"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-colors"
                ></textarea>
            </div>

            <x-select 
                label="Rol" 
                name="role" 
                id="edit_role"
                required 
            >
                <option value="admin">Administrador</option>
                <option value="peasant">Campesino</option>
            </x-select>

            <x-input 
                label="Nueva Contraseña (dejar vacío para mantener la actual)" 
                name="password" 
                type="password" 
                id="edit_password"
            />

            <x-input 
                label="Confirmar Nueva Contraseña" 
                name="password_confirmation" 
                type="password" 
                id="edit_password_confirmation"
            />

            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    name="active" 
                    id="edit_active" 
                    value="1"
                    class="w-4 h-4 text-coffee-600 border-gray-300 rounded focus:ring-coffee-500"
                >
                <label for="edit_active" class="ml-2 text-sm text-gray-700">Usuario activo</label>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <button type="button" 
                        @click="$dispatch('close-modal', { id: 'editUserModal' }); document.body.style.overflow = ''"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Actualizar Usuario
                </button>
            </div>
        </div>
    </form>
</x-modal>

