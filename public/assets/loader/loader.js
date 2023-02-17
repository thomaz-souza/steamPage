const Loader = function (block, mode)
{
	e = document.querySelector(block);
	if(mode)
	{
		e.style.opacity = 1;
		e.style.display = "flex";
	}
	else
	{
		e.style.opacity = 0;
		setTimeout(function(){ e.style.display = "none"; }, 410);
	}
}