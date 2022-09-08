function getFormattedDate(date) {
	date = new Date(date);
    let year = date.getFullYear();
    let month = (1 + date.getMonth()).toString().padStart(2, '0');
    let day = date.getDate().toString().padStart(2, '0');
    return month + '/' + day + '/' + year;
}

function getTypeAction(cell) {
		
	if(cell == undefined){
		return '/';
	}
	else
	{
		return cell;
	}
}