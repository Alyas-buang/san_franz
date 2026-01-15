<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JB Builders | Inventory System</title>

<style>
:root {
  --primary: #007bff;
  --secondary: #00bcd4;
  --white: #ffffff;
}

* {
  box-sizing: border-box;
  font-family: Arial, sans-serif;
}

body {
  margin: 0;
  min-height: 100vh;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--white);
}

.container {
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(10px);
  padding: 50px 40px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 20px 40px rgba(0,0,0,0.2);
  max-width: 420px;
  width: 90%;
}

h1 {
  margin-bottom: 10px;
  font-size: 2.3em;
}

p {
  opacity: 0.9;
  margin-bottom: 30px;
}

.btn {
  display: inline-block;
  padding: 15px 40px;
  background: var(--white);
  color: var(--primary);
  border-radius: 10px;
  font-weight: bold;
  text-decoration: none;
  letter-spacing: 1px;
  transition: all 0.3s ease;
}

.btn:hover {
  background: var(--primary);
  color: var(--white);
  transform: translateY(-3px);
}

footer {
  margin-top: 30px;
  font-size: 0.85em;
  opacity: 0.8;
}
</style>
</head>

<body>
  <div class="container">
    <h1>JB Builders</h1>
    <p>San Francisco Branch<br>Inventory Management System</p>

    <a href="inventory.php" class="btn">Enter System</a>

    <footer>
      © 2025 JB Builders<br>
      Developed by Dhale Joshua
    </footer>
  </div>
</body>
</html>
