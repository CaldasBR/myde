{adi:if $adiinviter->error->show_error == true}
<script type="text/javascript">
adjq(document).ready(function(){
	adi.show_ip_err('{adi:var $adiinviter->error->errors[0]}');
});
</script>
{/adi:if}