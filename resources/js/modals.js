window.Modals = {

    open(title, content) {

        $('#modalGlobalTitle').text(title);

        $('#modalGlobalBody').html(content);

        $('#modalGlobal').modal('show');

    }

};