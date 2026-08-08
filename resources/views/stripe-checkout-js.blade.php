<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.pay-invoice-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            var invoiceId = this.getAttribute('data-invoice-id');
            if (!invoiceId) return;
            var self = this;
            self.disabled = true;
            fetch('/invoices/' + invoiceId + '/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            }).then(function (res) { return res.json(); })
              .then(function (data) {
                  if (data.url) {
                      window.location = data.url;
                  } else if (data.error) {
                      alert('Error: ' + (data.error || 'Unknown'));
                      self.disabled = false;
                  }
              }).catch(function (err) {
                  alert('Request failed');
                  self.disabled = false;
              });
        });
    });
});
</script>
