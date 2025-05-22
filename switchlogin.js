  
        // Funcionalidad del switch
        const loginSwitchBtn = document.getElementById('loginSwitchBtn');
        const registerSwitchBtn = document.getElementById('registerSwitchBtn');
        const formSwitchContainer = document.getElementById('formSwitchContainer');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const showRegister = document.getElementById('showRegister');
        const showLogin = document.getElementById('showLogin');
        
        function switchToLogin() {
            formSwitchContainer.classList.remove('switch-to-register');
            loginSwitchBtn.classList.add('active');
            registerSwitchBtn.classList.remove('active');
            loginForm.classList.add('active');
            registerForm.classList.remove('active');
        }
        
        function switchToRegister() {
            formSwitchContainer.classList.add('switch-to-register');
            registerSwitchBtn.classList.add('active');
            loginSwitchBtn.classList.remove('active');
            registerForm.classList.add('active');
            loginForm.classList.remove('active');
        }
        
        loginSwitchBtn.addEventListener('click', switchToLogin);
        registerSwitchBtn.addEventListener('click', switchToRegister);
        showRegister.addEventListener('click', function(e) {
            e.preventDefault();
            switchToRegister();
        });
        showLogin.addEventListener('click', function(e) {
            e.preventDefault();
            switchToLogin();
        });
    