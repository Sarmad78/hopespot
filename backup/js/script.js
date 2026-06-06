// Fetch and include the header
fetch('header.html')
.then(response => response.text())
.then(html => {
  // Insert the header content into the container
  document.getElementById('header-container').innerHTML = html;
  
  // Attach event listeners for opening and closing the side menu
  document.getElementById("menuBtn").addEventListener("click", function() {
    document.getElementById("sideMenu").style.width = "250px";
  });

  document.getElementById("closeBtn").addEventListener("click", function() {
    document.getElementById("sideMenu").style.width = "0";
  });
})
.catch(error => {
  console.error('Error fetching header:', error);
});

/***** slider*****/
function currentSlide1(){
  document.getElementById("slide1").style.display="block";
  document.getElementById("slide2").style.display="none";
  document.getElementById("slide3").style.display="none";
  
 }


 function currentSlide2(){
     
  document.getElementById("slide1").style.display="none";
  document.getElementById("slide2").style.display="block";
  document.getElementById("slide3").style.display="none";
 }
 function currentSlide3(){
     
  document.getElementById("slide1").style.display="none";
  document.getElementById("slide2").style.display="none";
  document.getElementById("slide3").style.display="block";

 }
     
