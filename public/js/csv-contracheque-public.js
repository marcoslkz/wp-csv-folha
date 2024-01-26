jQuery(document).ready(function ($) {
  $('#contracheque_month_form').submit(function (e) {
    e.preventDefault();
    var selectedMonth = $('#selected_month').val();
    var download_pdf = $('#download_pdf').val();

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
  });
});
