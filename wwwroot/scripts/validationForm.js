document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerForm');
    const password = document.getElementById('password');
    const repeatPassword = document.getElementById('repeat_password');
    const name = document.getElementById('name');
    const email = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    form.addEventListener('submit', function (e) {
        let isValid = true;

        // Валидация имени
        if (name.value.trim() === '') {
            name.classList.add('is-invalid');
            isValid = false;
        } else {
            name.classList.remove('is-invalid');
        }

        // Валидация email
        if (!emailRegex.test(email.value)) {
            email.classList.add('is-invalid');
            isValid = false;
        } else {
            email.classList.remove('is-invalid');
        }

        // Валидация пароля
        if (password.value.length < 6) {
            password.classList.add('is-invalid');
            isValid = false;
        } else {
            password.classList.remove('is-invalid');
        }

        // Совпадение паролей
        if (password.value !== repeatPassword.value) {
            repeatPassword.classList.add('is-invalid');
            isValid = false;
        } else {
            repeatPassword.classList.remove('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    // Валидация в реальном времени
    repeatPassword.addEventListener('input', function () {
        if (password.value !== repeatPassword.value) {
            repeatPassword.classList.add('is-invalid');
        } else {
            repeatPassword.classList.remove('is-invalid');
        }
    });

    password.addEventListener('input', function () {
        if (password.value.length < 6) {
            password.classList.add('is-invalid');
        } else {
            password.classList.remove('is-invalid');
            if (password.value !== repeatPassword.value && repeatPassword.value.length > 0) {
                repeatPassword.classList.add('is-invalid');
            } else {
                repeatPassword.classList.remove('is-invalid');
            }
        }
    });

    name.addEventListener('input', function () {
        if (name.value.trim() !== '') {
            name.classList.remove('is-invalid');
        }
    });

    email.addEventListener('input', function () {
        if (emailRegex.test(email.value)) {
            email.classList.remove('is-invalid');
        }
    });
});