document.getElementById("portfolios_ALL").addEventListener("click", function() {
    var elems = document.getElementsByClassName('portfolios_cat');
    for (var i=0;i<elems.length;i+=1){
        console.log(i);
        elems[i].style.display = 'none';
        }
  });