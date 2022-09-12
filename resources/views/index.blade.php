<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <strong>Inicio de pago getnet</strong><br>

    <input type="text" value="Luist Olave"><br>
    <input type="Telefono" value="89868937"><br>
    <input type="text" value="1900"><br>
    <button id="btn-buy">Pagar</button>

    <br><br><br>

    <button id="btn-consult">consultar</button>

</body>
<script>
    var _Url = "{{ url('/') }}/";
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    const btn_pagar = document.querySelector("#btn-buy")
    const btn_consultar = document.querySelector("btnbtn-consult")

    btn_pagar.addEventListener("click", (e) => {
        
        const auth = new Promise((resolve, reject) =>{
            $.ajax({
                // data: parameters,
                url:  _Url+"generate_token",
                type: "GET",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response){
                    resolve(response)
                }
            })
        })

       

        auth.then(res => {
            const data = res
            // console.log(res.processUrl)
            // console.log(res.requestId)
            window.location.href = res.processUrl
        })
    })



</script>

</html>