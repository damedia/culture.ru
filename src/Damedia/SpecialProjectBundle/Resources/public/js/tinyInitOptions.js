var tinyInitOptions = {
        selector: "textarea.createPage_blockTextarea",
        snippet: { selectFormUrl: "tinySelectForm.html",
                   types: [
                       { value: "news",       text: "Новость" },
                       { value: "theater",    text: "Театр" },
                       { value: "realMuseum", text: "Музей" },
                       { value: "museum",     text: "Вирутальный тур" },
                       { value: "artObject",  text: "Артефакт" },
                       { value: "lecture",    text: "Лекция" }
                   ]
        },
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table  paste", // contextmenu
            "snippet"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | snippetAdd snippetEdit"
};