<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>OCC - Office of Registrar</title>
</head>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
  }

  body {
    padding: 16px;
    background: rgb(227 242 253 / 0.5);
    height: 100vh;
  }

  .wrapper {
    background: white;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    border-radius: 16px;
    padding: 16px;
    height: 100%;
  }

  .bg-logo {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 2px;
    margin-bottom: 100px;
  }

  .logo {
    height: 80px;
    width: 80px;
  }

  h1 {
    font-weight: 700;
    font-size: 24px;
  }

  .text p {
    font-weight: 500;
    font-size: 16px;
  }

  .text span {
    font-weight: 400;
    font-size: 16px;
  }

  .text {
    display: flex;
    gap: 8px;
  }
</style>

<body>
  <div class="wrapper">
    <div class="bg-logo">
      <img class="logo" src="https://occ.edu.ph/wp-content/uploads/2020/12/OCC_LOGO-300x300.png" alt="occ_logo" />
      <h1>Office of Registrar</h1>
    </div>
    <div class="text">
      <p>Your password:</p>
      <span>{{ $password }}</span>
    </div>
  </div>
</body>
</html>