


$(function(){

	//Formata o valor na página de cadastro de despesa.
	// $('#valor').number( true, 2 );

	$('#valor').change(function(){
		console.log('Second change event...');
	});

	$('#valor').number( true, 2 );
});
