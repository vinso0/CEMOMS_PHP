function populateEditModal(user) {
    document.getElementById('edit-user-id').value = user.id || '';
    document.getElementById('edit-username').value = user.username || '';
    document.getElementById('edit-email').value = user.email || '';
    document.getElementById('edit-role').value = user.role || '';
    document.getElementById('edit-password').value = '';
}

function populateDeleteModal(user) {
    document.getElementById('delete-user-id').value = user.id || '';
    document.getElementById('delete-username').textContent = user.username || '';
}

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}