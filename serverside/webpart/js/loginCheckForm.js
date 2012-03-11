function loginCheckForm(obj){
	var returnValue = true;
	var login = obj.login.value;
	var password = obj.password.value;
	var error_msg = "Incorrectly filled fields:";
	if(login ==""){
		error_msg +="\nLogin ";
		returnValue = false;
	}
	if(password.length < 6){
		error_msg +="\nPassword";
		returnValue= false;
	}
	if(!returnValue){
		alert(error_msg);
	}
	return returnValue;
}
