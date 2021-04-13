export function trans(key)
{
	var keys = key.split(".");
  return keys.reduce(function(o, k){
    var next = o[k];
    if (!next) {
      console.error('No translation found for ' + key);
      return {};
    }
    else {
      return next;
    }
  }, topup_langs);
}