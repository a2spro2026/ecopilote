<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_configuration_page_shows_hero_video_bar(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.page.configuration'))
            ->assertOk()
            ->assertSee('Élément 1')
            ->assertSee('Ajouter une vidéo')
            ->assertSee('Parcourir')
            ->assertDontSee('accept="video/*"', false)
            ->assertSee('Activités');
    }

    public function test_uploaded_hero_video_is_shown_on_activites(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.configuration.video.store'), [
                'video' => UploadedFile::fake()->create('accueil.mp4', 1200, 'video/mp4'),
            ])
            ->assertRedirect(route('admin.page.configuration'))
            ->assertSessionHasNoErrors();

        $path = SiteSetting::getValue(SiteSetting::HERO_VIDEO);
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('images/hero-background.png', false)
            ->assertDontSee('hero-video-frame', false)
            ->assertDontSee('Découvrir nos formations')
            ->assertDontSee('Un enseignement de qualité');

        $this->get(route('activites'))
            ->assertOk()
            ->assertSee('hero-video-frame', false)
            ->assertSee('/media/accueil-video', false);

        $this->get(route('site.hero-video'))
            ->assertOk()
            ->assertHeader('content-type', 'video/mp4');
    }

    public function test_removing_hero_video_restores_the_home_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.configuration.video.store'), [
                'video' => UploadedFile::fake()->create('accueil.mp4', 800, 'video/mp4'),
            ])
            ->assertSessionHasNoErrors();

        $this->actingAs($this->admin())
            ->post(route('admin.configuration.video.destroy'))
            ->assertRedirect(route('admin.page.configuration'));

        $this->assertNull(SiteSetting::heroVideoUrl());
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('images/hero-background.png', false)
            ->assertDontSee('<video', false);

        $this->get(route('activites'))
            ->assertOk()
            ->assertSee('hero-video-frame', false)
            ->assertSee('Cadre de diffusion')
            ->assertDontSee('<video', false);
    }

    public function test_windows_video_files_are_accepted_even_with_a_generic_mime_type(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.configuration.video.store'), [
                'video' => UploadedFile::fake()->create('promo.mov', 900, 'application/octet-stream'),
            ])
            ->assertRedirect(route('admin.page.configuration'))
            ->assertSessionHasNoErrors();
    }

    public function test_desktop_videos_with_windows_mime_types_are_accepted(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.configuration.video.store'), [
                'video' => UploadedFile::fake()->create('Film Bureau.MP4', 1100, 'application/x-matroska'),
            ])
            ->assertRedirect(route('admin.page.configuration'))
            ->assertSessionHasNoErrors();
    }

    public function test_audio_only_mp4_is_rejected(): void
    {
        Storage::fake('public');
        $tmp = tempnam(sys_get_temp_dir(), 'aud');
        file_put_contents($tmp, 'ftypiso6mp41'.str_repeat('a', 80).'smhd'.'soun');

        $this->actingAs($this->admin())
            ->post(route('admin.configuration.video.store'), [
                'video' => new UploadedFile($tmp, 'chanson.mp4', 'video/mp4', null, true),
            ])
            ->assertSessionHasErrors('video');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    }
}
