const { EOL } = require('os')
const fs = require('fs')

fs.readFile('./ffmpeg-formats.txt', 'utf-8', (err, data) => {
	if (err) {
		console.error(err)
		return
	}

	const media = {
		photo: { from: [], to: [] },
		audio: { from: [], to: [] },
		video: { from: [], to: [] },
	}

	data.split(EOL).forEach((line, index) => {
		line = line.trim()
		const flags = line.substring(0, 6)
		const pieces = line.replace(flags, '').trim().split(' ')
		const extension = pieces[0].trim()
		const label = pieces.slice(1).join(' ').trim()

		const format = {
			extension,
			label,
			compression: flags[4] === 'L' ? 'lossy' : flags[5] === 'S' ? 'lossless' : null,
		}

		if (flags[0] === 'D') {
			if (flags[2] === 'V') {
				media.video.from.push(format)
			}
			if (flags[2] === 'A') {
				media.audio.from.push(format)
			}
		}

		if (flags[1] === 'E') {
			if (flags[2] === 'V') {
				media.video.to.push(format)
			}
			if (flags[2] === 'A') {
				media.audio.to.push(format)
			}
		}
	})

	fs.writeFile('formats.json', JSON.stringify(media, null, 4), (err) => {
		if (err) {
			console.error(err)
			return
		}

		console.log('done')
	})
})
