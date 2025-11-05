<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - S-Core ITBSS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6'
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div class="flex h-screen" x-data="loginForm()">
        <!-- Left Side - Campus Image -->
        <div class="w-1/2 bg-cover bg-center" style="background-image: url('/images/campus.png')"></div>

        <!-- Right Side - Login Form -->
        <div class="w-1/2 flex flex-col justify-center items-center bg-gray-50">
            <div class="w-80">
                <div class="text-center mb-8">
                    <img src="/images/logo.png" alt="Logo" class="mx-auto w-24">
                    <h1 class="text-2xl font-bold mt-3 text-gray-800">S-Core ITBSS</h1>
                    <p class="text-gray-500 text-sm">Sabda Setia Student Point System</p>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label class="text-gray-600 text-sm">Email</label>
                        <input
                            type="email"
                            x-model="email"
                            @input="emailError = false; error = ''"
                            :class="emailError ? 'border-red-500 focus:ring-red-500' : 'focus:ring-primary'"
                            class="w-full border rounded-md p-2 mt-1 focus:outline-none focus:ring-1"
                        />
                        <p x-show="emailError" class="text-red-500 text-xs mt-1">Email must be filled in</p>
                    </div>

                    <div>
                        <label class="text-gray-600 text-sm">Password</label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                x-model="password"
                                @input="passwordError = false; error = ''"
                                :class="passwordError ? 'border-red-500 focus:ring-red-500' : 'focus:ring-primary'"
                                class="w-full border rounded-md p-2 mt-1 pr-10 focus:outline-none focus:ring-1"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                            >
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <p x-show="passwordError" class="text-red-500 text-xs mt-1">Password must be filled in</p>
                    </div>

                    <p x-show="error" x-text="error" class="text-red-500 text-sm"></p>

                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between items-center text-sm">
                            <a href="#" class="text-primary hover:underline">
                                Forgot your password?
                            </a>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-1">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-primary text-white py-2 rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200"
                    >
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                email: '',
                password: '',
                error: '',
                emailError: false,
                passwordError: false,
                showPassword: false,
                loginAsAdmin: false,

                async handleSubmit() {
                    this.error = '';
                    this.emailError = false;
                    this.passwordError = false;

                    // Validate empty inputs
                    if (!this.email.trim()) {
                        this.emailError = true;
                        return;
                    }

                    if (!this.password) {
                        this.passwordError = true;
                        return;
                    }

                    try {
                        // For development: redirect directly
                        // In production, you would make an API call here
                        // const response = await fetch('/api/login', {
                        //     method: 'POST',
                        //     headers: { 'Content-Type': 'application/json' },
                        //     body: JSON.stringify({ email: this.email, password: this.password })
                        // });

                        // Temporary: redirect based on email
                        if (this.email.includes('admin')) {
                            window.location.href = '/admin';
                        } else {
                            window.location.href = '/dashboard';
                        }
                    } catch (err) {
                        this.error = 'Incorrect email address or password';
                    }
                }
            }
        }
    </script>
</body>
</html>
