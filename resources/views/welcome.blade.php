<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form id="loginForm">
        <label>Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        
        <label>Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit">Login</button>
    </form>

    <p id="message"></p>

    <script>
        const form = document.getElementById('loginForm');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('http://localhost:8000/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if(response.ok){
                    document.getElementById('message').textContent = 'Login successful!';
                    console.log('Access Token:', data.access_token);
                } else {
                    document.getElementById('message').textContent = data.message || 'Login failed!';
                }

            } catch (error) {
                document.getElementById('message').textContent = 'Error: ' + error;
            }
        });
    </script>
</body>
</html>
