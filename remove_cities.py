import re

with open('resources/views/user/dashboard.blade.php', 'r', encoding='utf-8') as f:
    html = f.read()

# Remove the Boarding and Destination city blocks and replace with hidden inputs
city_block_regex = r'<div>\s*<label[^>]*>Boarding City \(From\) \*</label>.*?</select>\s*</div>'
html = re.sub(city_block_regex, '<input type="hidden" name="from_city_id" :value="fromCityId">', html, flags=re.DOTALL)

dest_block_regex = r'<div>\s*<label[^>]*>Destination City \(To\) \*</label>.*?</select>\s*</div>'
html = re.sub(dest_block_regex, '<input type="hidden" name="to_city_id" :value="toCityId">', html, flags=re.DOTALL)

with open('resources/views/user/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(html)
