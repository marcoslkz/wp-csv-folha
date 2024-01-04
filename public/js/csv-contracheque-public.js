jQuery(document).ready(function ($) {
  $('#contracheque_month_form').submit(function (e) {
    e.preventDefault();
    var selectedMonth = $('#selected_month').val();
    var download_pdf = $('#download_pdf').val();

    var downloadPdfValue = parseInt($('#download_pdf').val());
    if (downloadPdfValue > 0) {
      $.ajax({
        url: ajax_obj.ajax_url, // Use the AJAX URL passed from wp_localize_script

        type: 'POST',
        data: {
          action: 'contracheque_get_data',
          cpf: '00',
          selectedMonth: selectedMonth,
          download_pdf: 0,
        },
        success: function (response) {
          downloadFile(response, selectedMonth);
        },
        error: function (xhr, status, error) {
          console.error(xhr.responseText);
          // Handle errors here
        }
      });
    } else {
      $.ajax({
        url: ajax_obj.ajax_url, // Use the AJAX URL passed from wp_localize_script
        type: 'POST',
        data: {
          action: 'contracheque_get_data',
          selected_month: selectedMonth,
          download_pdf: download_pdf,
        },
        success: function (response) {
          // Update the table with the response
          $('#contracheque_result_table').html(response);
        },
        error: function (xhr, status, error) {
          console.error(xhr.responseText);
          // Handle errors here
        }
      });
    }
  });

  function downloadFile(resp, month) {
    // create a download anchor tag
    var downloadLink = document.createElement('a');
    //downloadLink.target   = '_blank';
    downloadLink.download = 'contracheque.pdf';
    // convert downloaded data to a Blob
    var blob = new Blob([resp], { type: 'text/plain' });
    var URL = window.URL || window.webkitURL;
    var downloadUrl = URL.createObjectURL(blob.data);

    // set object URL as the anchor's href
    downloadLink.href = downloadUrl;

    // append the anchor to document body
    document.body.append(downloadLink);

    // fire a click event on the anchor
    downloadLink.click();

    // cleanup: remove element and revoke object URL
    document.body.removeChild(downloadLink);
    URL.revokeObjectURL(downloadUrl);
  }
});
