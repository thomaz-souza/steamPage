window.assembler = {

	plotter : function (d,m,e,r)
	{
		if(e&&!r) $(e).html('');
		b=[];
		for(var i=0; i<d.length; i++)
			b.push(m(d[i]));
		r=b.join('');			
		if(e) $(e).html(r);
		return r;
	},
	zero: function (v) {
		v = Number(v);
		if(v<10&&v>-10) return "0"+v;
		return v;
	},
	getDate: function(d)
	{
		let s = d.replace(/[ :]/g, "-").split("-");
    	return new Date(s[0], s[1]-1, s[2], s[3] ? s[3] : 0, s[4] ? s[4] : 0, s[5] ? s[5] : 0);
	},
	formatDate : function (ts,m)
	{
		if(ts instanceof Date)
			d = ts;
		else if(typeof ts == "string")
			d = this.getDate(ts);
		else
			return "";

		nd=[]; ms=m.split('');
		for(i=0;i<ms.length;i++)
		{
			switch(ms[i])
			{
				case 'd':
					v=this.zero(d.getDate());
				break;
				case 'm':
					v=this.zero(d.getMonth()+1);
				break;
				case 'Y':
					v=d.getFullYear();
				break;
				case 'y':
					v=d.getFullYear().substr(2,2);
				break;
				case 'h':
				case 'H':
					v=this.zero(d.getHours());
				break;
				case 'i':
					v=this.zero(d.getMinutes());
				break;
				case 's':
					v=this.zero(d.getSeconds());
				break;
				default:
					v=ms[i];
				break;
			}
			nd.push(v);
		}
		return nd.join('');
	},
	pagination : function (c) {
	    let current = Number(c.currentPage),
	        last = Number(c.totalPages),
	        delta = 1,
	        left = Number(current) - delta,
	        right = Number(current) + delta + 1,
	        range = [],
	        rangeWithDots = [],
	        l;

	    for (let i = 1; i <= last; i++) {
	        if ( (i == 1 || i == last) || (i >= left && i < right)) {
	            range.push(i);
	        }
	    } 

	    for (let r of range) {
	        if (l) {
	            if (r - l === 2) {
	                rangeWithDots.push(l + 1);
	            } else if (r - l !== 1) {
	            	rangeWithDots.push('...');
	            }
	        }
	        rangeWithDots.push(r);
	        l = r;
	    }
	    return rangeWithDots;
	}
}