function changeCat(id1,id2,tb1,tb2)
    {
    cat1 = document.getElementById(id1);
    cat2 = document.getElementById(id2);
    cat1.style.display = "block";
    cat2.style.display = "none";
    document.getElementById(tb1).className = "active";
    document.getElementById(tb2).className = document.getElementById(tb2).className.replace( /(?:^|\s)active(?!\S)/ , '' );
    }