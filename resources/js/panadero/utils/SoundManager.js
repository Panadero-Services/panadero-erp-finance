class SoundManager {
    constructor() {
        this.sounds = {
            shoot: new Audio('/sounds/collect.mp3'),
            explosion: new Audio('/sounds/explosion.mp3'),
            powerup: new Audio('/sounds/powerup.mp3'),
            collect: new Audio('/sounds/collect.mp3'),
            background: new Audio('/sounds/background.mp3')
        };

        // Configure background music
        this.sounds.background.loop = true;
        this.sounds.background.volume = 0.3; // 30% volume for background

        // Configure sound effects
        this.sounds.shoot.volume = 0.4;
        this.sounds.explosion.volume = 0.4;
        this.sounds.powerup.volume = 0.5;

        // Initialize mute state
        this.muted = false;
    }

    playSound(soundName) {
        if (this.muted) return;
        
        const sound = this.sounds[soundName];
        if (sound) {
            // For sound effects, reset and play
            if (soundName !== 'background') {
                sound.currentTime = 0;
            }
            sound.play().catch(err => console.log('Audio play error:', err));
        }
    }

    startBackgroundMusic() {
        if (!this.muted) {
            this.sounds.background.play().catch(err => console.log('Background music error:', err));
        }
    }

    stopBackgroundMusic() {
        this.sounds.background.pause();
        this.sounds.background.currentTime = 0;
    }

    toggleMute() {
        this.muted = !this.muted;
        Object.values(this.sounds).forEach(sound => {
            sound.muted = this.muted;
        });
        
        if (this.muted) {
            this.stopBackgroundMusic();
        } else {
            this.startBackgroundMusic();
        }
        
        return this.muted;
    }

    setVolume(volume) {
        const normalizedVolume = Math.max(0, Math.min(1, volume));
        Object.values(this.sounds).forEach(sound => {
            sound.volume = normalizedVolume;
        });
    }
}// Create and export a single instance
export const soundManager = new SoundManager();

