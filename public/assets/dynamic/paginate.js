var paginate = function (p,y)
{
	let self = this;

	self.set = function (p,y)
	{
		self._y = y;
		for(let i=0;i<Object.keys(p).length;i++)
			eval('self["' + Object.keys(p)[i] + '"] = function(){ return ' + Number(p[Object.keys(p)[i]]) + '; };');	
	}

	self.isFirst = function()
	{
		return (self.currentPage() == 1);
	}

	self.isLast = function()
	{
		return (self.currentPage() == self.totalPages());
	}

	self.delta = function (delta)
	{
		if(!delta)
		{
			if(!self._d)
				return self._d = 1;
			return self._d;
		}
		return self._d = Number(delta);
	}

	self.separator = function (s)
	{
		if(!s)
		{
			if(!self._s)
				return self._s = '...';
			return self._s;
		}
		return self._s = s;

	}
	
	self.paginate = function (s, d) 
	{
		let delta = self.delta(d),
			separator = self.separator(s);

		let
	        last = self.totalPages(),
	        left = self.currentPage() - delta,
	        right =self.currentPage() + delta + 1,
	        range = [],
	        rangeWithDots = [],
	        l;

	    for (let i = 1; i <= last; i++) {
	        if ( (i == 1 || i == last) || (i >= left && i < right)) {
	            range.push(i);
	        }
	    }

	    for (let i of range) {
	        if (l) {
	            if (i - l === 2) {
	                rangeWithDots.push(l + 1);
	            } else if (i - l !== 1) {
	            	rangeWithDots.push(separator);
	            }
	        }
	        rangeWithDots.push(i);
	        l = i;
	    }
	    return rangeWithDots;
	}

	self.paginateOn = function (e, separator, delta)
	{
		let p = self.paginate(separator, delta);
		$(e).html('');
		for(let n=0; n<p.length; n++)
		{
			i = p[n];
			let b = document.createElement('div');

			if(i === self.separator())
			{
				if(separator===!1) continue;
				b.className = 'paginate-item-separator';
			}
			else if(self.currentPage()==i)
			{
				b.className = 'paginate-item-page current-page';
			}
			else
			{
				b.className = 'paginate-item-page';
				eval('b.onclick=function(){ self._y.page("'+i+'"); }');	
			}
			b.innerHTML = i;			
			$(e).append(b);
		}
	}
	
	self.set(p,y)
}