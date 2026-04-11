from pathlib import Path

path = Path('app/Views/candidate/dashboard.php')
text = path.read_text(encoding='utf-8')
marker = "<?= view('Layouts/candidate_footer') ?>"
idxs = []
start = 0
while True:
    i = text.find(marker, start)
    if i == -1:
        break
    idxs.append(i)
    start = i + len(marker)

print('markers', len(idxs), idxs)
if len(idxs) > 1:
    new_text = text[:idxs[0]] + text[idxs[1]:]
    path.write_text(new_text, encoding='utf-8')
    print('removed duplicate block; new marker count', new_text.count(marker))
else:
    print('no duplicate block found')
