$("#registerForm").on("submit", function(e){
  e.preventDefault();
  const data = {
    name: this.name.value.trim(),
    email: this.email.value.trim(),
    password: this.password.value
  };
  $.ajax({
    url: "php/register.php",
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify(data),
    success: (res)=>{
      $("#regAlert").removeClass("d-none alert-danger").addClass("alert-success").text(res.message || "Registered!");
      setTimeout(()=> location.href="login.html", 800);
    },
    error: (xhr)=>{
      $("#regAlert").removeClass("d-none alert-success").addClass("alert-danger").text(xhr.responseJSON?.error || "Error");
    }
  });
});
