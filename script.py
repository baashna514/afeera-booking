import re

with open('resources/views/user/dashboard.blade.php', 'r', encoding='utf-8') as f:
    html = f.read()

# 1. Update grid direction to RTL
html = html.replace('<div class="grid grid-cols-5 gap-3">', '<div class="grid grid-cols-5 gap-3" dir="rtl">')
html = html.replace('<div class="grid grid-cols-5 gap-2">', '<div class="grid grid-cols-5 gap-2" dir="rtl">')

# 2. Update backRow logic so seat 35 & 36 are together
html = html.replace('if (this.seatMap.length > 5) {', 'let backRowSize = (this.seatMap.length % 4 === 1) ? 5 : 0;\n                if (backRowSize > 0 && this.seatMap.length > backRowSize) {')
html = html.replace('this.mainSeats = this.seatMap.slice(0, this.seatMap.length - 5);', 'this.mainSeats = this.seatMap.slice(0, this.seatMap.length - backRowSize);')
html = html.replace('this.backSeats = this.seatMap.slice(this.seatMap.length - 5);', 'this.backSeats = this.seatMap.slice(this.seatMap.length - backRowSize);')

# 3. Restructure Layout
# Remove the old 3-col wrapper
html = html.replace('<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">', '<div class="flex flex-col gap-6">')

# Make Search Route full width
html = html.replace('<div class="lg:col-span-1 space-y-6">', '<div class="w-full space-y-6">')

# Change inner classes of Step 1 to grid so it looks like a top bar
html = html.replace('<div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-5">', '<div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">')
html = html.replace('<h3 class="font-black text-slate-900 text-lg flex items-center gap-2 pb-3 border-b border-slate-100">', '<h3 class="font-black text-slate-900 text-lg flex items-center justify-between gap-2 pb-3 border-b border-slate-100 mb-5">')
html = html.replace('<span>Select Journey</span>\n                </h3>', '<span>Select Journey</span>\n                    <button type="button" @click="reloadSeatMap()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-2">\n                        <i class="fa-solid fa-rotate-right"></i> Refresh Seats\n                    </button>\n                </h3>\n                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">')
html = html.replace('<!-- Available Departure Schedules -->', '</div><!-- End Top Grid -->\n                <!-- Available Departure Schedules -->')
html = html.replace('<div class="space-y-2">', '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">')

html = html.replace('<div class="lg:col-span-2 space-y-6">', '<div class="w-full space-y-6">')

# 4. Swap form and map
bus_start = html.find('<!-- Bus Layout Container -->')
form_start = html.find('<!-- Ticket Confirmation Form -->')
form_end = html.find('<!-- Expense Modal -->')

if bus_start != -1 and form_start != -1 and form_end != -1:
    bus_layout = html[bus_start:form_start]
    # We find the end of the form. It ends right before Expense Modal.
    # The actual form tag ends somewhere. Let's just grab the whole thing.
    # The div closing the block is at form_end - 25 approx.
    ticket_form = html[form_start:form_end-25]
    
    new_layout = f'''
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pt-6">
        <!-- LEFT COLUMN: Ticket Confirmation Form (Passenger Info) -->
        <div class="lg:col-span-7 order-2 lg:order-1">
            {ticket_form.replace('class="pt-4 border-t border-slate-100 space-y-5"', 'class="space-y-5"')}
        </div>
        
        <!-- RIGHT COLUMN: Bus Layout Container -->
        <div class="lg:col-span-5 order-1 lg:order-2">
            {bus_layout}
        </div>
    </div>
    '''
    
    html = html[:bus_start] + new_layout + html[form_end-25:]

with open('resources/views/user/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(html)
