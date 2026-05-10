// Capturamos el evento de envío del formulario
document.getElementById('pagoForm').addEventListener('submit', async function(e) {
    e.preventDefault(); // Evita que la página se recargue

    // Elementos de la interfaz
    const btnPagar = document.getElementById('btnPagar');
    const divResultado = document.getElementById('resultado');
    
    // Cambiamos el estado del botón para dar feedback al usuario
    btnPagar.disabled = true;
    btnPagar.innerText = "Procesando de forma segura...";
    divResultado.classList.add('hidden');

    // Recolectamos los datos del formulario
    const datosPago = {
        idAcudiente: document.getElementById('idAcudiente').value,
        monto: parseFloat(document.getElementById('monto').value),
        concepto: document.getElementById('concepto').value
    };

    try {
        // Hacemos la petición a nuestro puente intermediario (BFF)
        const response = await fetch('api_pagos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datosPago)
        });

        const data = await response.json();

        // Limpiamos clases previas
        divResultado.className = "mt-6 p-4 rounded-md text-sm";
        divResultado.classList.remove('hidden');

        // Renderizamos la respuesta basada en el estado
        if (data.exito) {
            divResultado.classList.add('bg-green-50', 'text-green-800', 'border', 'border-green-200');
            divResultado.innerHTML = `
                <h3 class="font-bold mb-1">¡Pago Aprobado!</h3>
                <p><strong>Recibo:</strong> ${data.comprobante}</p>
                <p><strong>Estado:</strong> ${data.estado}</p>
            `;
            document.getElementById('pagoForm').reset(); // Limpia el formulario
        } else {
            divResultado.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-200');
            divResultado.innerHTML = `
                <h3 class="font-bold mb-1">Transacción Rechazada</h3>
                <p>${data.mensaje}</p>
            `;
        }

    } catch (error) {
        // Manejo de errores de red o servidor caído
        divResultado.className = "mt-6 p-4 rounded-md text-sm bg-red-50 text-red-800 border border-red-200";
        divResultado.classList.remove('hidden');
        divResultado.innerHTML = `<p>Error de conexión con el servidor. Intente nuevamente.</p>`;
        console.error("Error en Fetch:", error);
    } finally {
        // Restauramos el botón sin importar si falló o fue exitoso
        btnPagar.disabled = false;
        btnPagar.innerText = "Procesar Pago";
    }
});