<!DOCTYPE html>
<html lang="en">
  <head>
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
  </head>
  <body>
    <button type="button" id="renderBtn">
      Pay Now
    </button>
  </body>
  <script>
      const cashfree = Cashfree({
        mode: "production" //or production,
      });
      document.getElementById("renderBtn").addEventListener("click", () => {
        cashfree.checkout({
          paymentSessionId: "{{ app('request')->input('paymentSessionId')}}"
        });
      });
  </script>
</html>