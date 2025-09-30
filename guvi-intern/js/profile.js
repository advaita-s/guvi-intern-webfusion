const token = localStorage.getItem("token");
if(!token){ location.href = "login.html"; }

function setHeader(xhr){ xhr.setRequestHeader("X-Auth-Token", token); }

// Load profile
$.ajax({
  url: "php/profile.php",
  method: "GET",
  beforeSend: setHeader,
  success: (res)=>{
    $("#uName").text(res.user.name);
    $("#uEmail").text(res.user.email);
    $("input[name=age]").val(res.profile.age ?? "");
    $("input[name=dob]").val(res.profile.dob ?? "");
    $("input[name=contact]").val(res.profile.contact ?? "");
  },
  error: ()=> { localStorage.clear(); location.href = "login.html"; }
});

// Update profile
$("#profileForm").on("submit", function(e){
  e.preventDefault();
  const data = {
    age: parseInt(this.age.value || "0", 10) || null,
    dob: this.dob.value || null,
    contact: this.contact.value.trim() || null
  };
  $.ajax({
    url: "php/profile.php",
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify(data),
    beforeSend: setHeader,
    success: ()=> $("#profAlert").removeClass("d-none alert-danger").addClass("alert-success").text("Saved!"),
    error: ()=> $("#profAlert").removeClass("d-none alert-success").addClass("alert-danger").text("Error")
  });
});

// Logout
$("#logoutBtn").on("click", ()=>{
  localStorage.clear();
  location.href = "login.html";
});
