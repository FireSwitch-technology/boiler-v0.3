<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Email</title>
    <link
      rel="stylesheet"
      href="	https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
      rel="stylesheet"
    />
  </head>

  <style>
    body {
      font-family: 'Inter', sans-serif !important;
      padding: 0px;
      margin: 0px;
    }
    .email {
      width: 100%;
      display: flex;
      justify-content: center;
    }
    .borders {
      width: 800px;
      background-color: white;
    }
    .logo {
      text-align: center;
      width: 100%;
      padding: 10px 30px;
    }
    .logo div {
      text-align: center;
      margin: auto;
      width: 200px;
      height: 100px;
    }
    .logo img {
      text-align: center;
      width: 100%;
    }
    .main-img {
      width: 300px;
      height: 100%;
    }
    .main-img img {
      text-align: center;
      width: 300px;
      height: 100%;
    }
    .borders h1 {
      margin-top: 10px;
      color: #737373;
      font-size: 24px;
      font-style: normal;
      font-weight: 600;
      text-align: center;
    }
    li {
      color: #737373;
      font-size: 17px;
      list-style-type: none;
    }
    p {
      color: #737373;
      font-size: 17px;
    }
    .light {
      color: rgb(78, 195, 235);
    }
    .wine {
      color: purple;
      font-weight: bold;
    }
    .ass {
      color: rgb(78, 195, 235);
      font-weight: bold;
    }
    h3 {
      color: black;
      font-size: 30px;
      font-weight: bold;
    }
    i {
      color: #737373;
      font-size: 17px;
    }
    .world {
      color: #036;
      font-weight: bold;
    }
    .xplore {
      color: #e20505;
      font-weight: bold;
    }
    .btn-primary {
      font-size: 24px; /* Adjust the font size to make it larger */
      padding: 1px 34px; /* Adjust the padding for the button */
    }
  </style>
  <body>
    <section class="email">
      <main class="borders shadow">
        <div class="logo">
          <div>
            <a href="https://worldxplorer.co"
              ><img
                src="https://worldxplorer.co/img/wxp_logo.png"
                alt="WorldXplorer Logo"
            /></a>
          </div>
        </div>
        <section>
          <main class="p-3">
            <p></p>
            <h3>
              🌍 Welcome to WorldXplorer,
              <?php echo "latin"; $fname ?>!
            </h3>
            <p>
              We are thrilled to have you as a member of our travel community.
              Your registration is complete, and you're now part of the
              WorldXplorer family.
            </p>
            <p>
              WorldXplorer is not just about booking flights; we are about
              crafting memorable travel experiences. Whether you are planning a
              relaxing getaway, an epic adventure, or a business trip, we have
              got you covered.
            </p>
            <p>
              If you have any questions or need assistance, our dedicated
              support team is here to help. Feel free to explore our website and
              discover amazing travel opportunities.
            </p>
            <p>
              Thank you for choosing WorldXplorer. Let's explore the world
              together!
            </p>
            <h5>
              <span class="ass">The WorldXplorer</span><br />
              <i>Igniting Passionate Journeys</i>
            </h5>
          </main>
        </section>
      </main>
    </section>
  </body>
</html>

?>
