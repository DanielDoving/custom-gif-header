$(document).ready(function () {
    $('#bg-info-span').on('click', function () {
        Swal.fire({
                      title            : 'Change Keyword',
                      showDenyButton   : true,
                      showCancelButton : true,
                      confirmButtonText: 'Random Keyword from Wordlist',
                      denyButtonText   : 'Enter a custom Keyword',
                  }).then((result) => {
            if (result.isConfirmed) {
                let url = new URL(window.location.href);
                url.searchParams.append('force_refresh_background', true);
                window.location = url.toString();
            }
            else if (result.isDenied) {
                Swal.fire({
                              title           : 'Enter Keyword',
                              showCancelButton: true,
                              input           : 'text',
                              inputValidator  : (value) => {
                                  if (!value) {
                                      return 'You need to write something!'
                                  }
                              },
                          }).then((result) => {
                    let url = new URL(window.location.href);
                    url.searchParams.append('bg-keyword', result.value);
                    window.location = url.toString();
                });
            }
        });
    });
})

