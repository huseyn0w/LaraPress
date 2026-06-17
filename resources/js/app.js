import './bootstrap';

import Alpine from 'alpinejs';
import { registerFrontComponents } from './front';

// Register the public-theme Alpine components (like / comment / reveal / menu).
registerFrontComponents(Alpine);

window.Alpine = Alpine;

Alpine.start();
