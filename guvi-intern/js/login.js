$("#loginForm").on("submit", function(e){
  e.preventDefault();

  const formData = {
    email: $("input[name=email]").val().trim(),
    password: $("input[name=password]").val()
  };

  console.log("Sending:", formData); // Debug in browser console

  $.ajax({
    url: "php/login.php",
    method: "POST",
    data: JSON.stringify(formData),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    success: function(res){
      console.log("Response:", res); // Debug log
      localStorage.setItem("token", res.token);
      localStorage.setItem("user", JSON.stringify(res.user));
      window.location.href = "profile.html";
    },
    error: function(xhr){
      console.log("Error:", xhr.responseText); // Debug log
      $("#loginAlert")
        .removeClass("d-none alert-success")
        .addClass("alert-danger")
        .text(xhr.responseJSON?.error || "Invalid credentials");
    }
  });
});
