<?php

namespace Tests\Feature;

use App\Models\Oficio;
use App\Models\EstadoOficio;
use App\Models\User;
use App\Services\OficioArchivoStorageService;
use App\Mail\OficioTurnadoMail;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OficioSensitiveAccessTest extends TestCase
{
    private int $receivedTypeId;
    private int $sentTypeId;
    private int $registeredStateId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();

        $this->sentTypeId = DB::table('tipos_oficio')->insertGetId([
            'clave' => 'enviado',
            'nombre' => 'Enviado',
            'activo' => true,
        ]);
        $this->receivedTypeId = DB::table('tipos_oficio')->insertGetId([
            'clave' => 'recibido',
            'nombre' => 'Recibido',
            'activo' => true,
        ]);
        DB::table('tipos_oficio')->insert([
            'clave' => 'recibido_cpc',
            'nombre' => 'Recibido CPC',
            'activo' => true,
        ]);

        DB::table('estados_oficio')->insert([
            [
                'clave' => 'registrado',
                'nombre' => 'Registrado',
                'color' => 'blue',
                'orden' => 1,
                'es_final' => false,
                'activo' => true,
            ],
            [
                'clave' => 'en_seguimiento',
                'nombre' => 'En seguimiento',
                'color' => 'blue',
                'orden' => 2,
                'es_final' => false,
                'activo' => true,
            ],
            [
                'clave' => 'turnado',
                'nombre' => 'Turnado',
                'color' => 'blue',
                'orden' => 3,
                'es_final' => false,
                'activo' => true,
            ],
            [
                'clave' => 'vencido',
                'nombre' => 'Vencido',
                'color' => 'red',
                'orden' => 4,
                'es_final' => false,
                'activo' => true,
            ],
            [
                'clave' => 'cerrado',
                'nombre' => 'Cerrado',
                'color' => 'gray',
                'orden' => 5,
                'es_final' => true,
                'activo' => true,
            ],
            [
                'clave' => 'cancelado',
                'nombre' => 'Cancelado',
                'color' => 'red',
                'orden' => 6,
                'es_final' => true,
                'activo' => true,
            ],
        ]);
        $this->registeredStateId = (int) DB::table('estados_oficio')
            ->where('clave', 'registrado')
            ->value('id');

        DB::table('tipos_participacion')->insert([
            [
                'clave' => 'responsable',
                'nombre' => 'Responsable',
                'implica_responsabilidad' => true,
                'activo' => true,
            ],
            [
                'clave' => 'apoyo',
                'nombre' => 'Apoyo operativo',
                'implica_responsabilidad' => false,
                'activo' => true,
            ],
            [
                'clave' => 'copiado',
                'nombre' => 'C.C.P.',
                'implica_responsabilidad' => false,
                'activo' => true,
            ],
            [
                'clave' => 'revision',
                'nombre' => 'Revisión',
                'implica_responsabilidad' => false,
                'activo' => true,
            ],
        ]);

        foreach ([
            'activo',
            'en_atencion',
            'atendido',
            'comunicado_externamente',
            'cerrado',
        ] as $state) {
            DB::table('estados_turnado')->insert([
                'clave' => $state,
                'nombre' => ucfirst($state),
                'activo' => true,
            ]);
        }
    }

    public function test_sensitive_details_redact_content_for_an_administrator_without_permission(): void
    {
        $oficioId = $this->createSensitiveOffice();
        $admin = $this->createUser();
        $roleId = DB::table('roles')->insertGetId([
            'clave' => 'admin',
            'nombre' => 'Administrador',
        ]);

        DB::table('usuario_roles')->insert([
            'usuario_id' => $admin->id,
            'rol_id' => $roleId,
        ]);

        $this->actingAs($admin)
            ->getJson(route('oficios.detalle.json', $oficioId))
            ->assertOk()
            ->assertJsonPath('asunto', 'Asunto administrativo')
            ->assertJsonPath('descripcion', null)
            ->assertJsonPath('link_documento', null)
            ->assertJsonPath('link_drive', null)
            ->assertJsonPath('puede_ver_contenido', false)
            ->assertJsonPath('tags', [])
            ->assertJsonPath('archivos', []);
    }

    public function test_sensitive_details_include_content_for_a_user_with_explicit_permission(): void
    {
        $oficioId = $this->createSensitiveOffice();
        $user = $this->createUser();
        $permissionId = DB::table('permisos')->insertGetId([
            'clave' => 'puede_ver_sensibles',
            'nombre' => 'Puede ver sensibles',
        ]);

        DB::table('usuario_permisos')->insert([
            'usuario_id' => $user->id,
            'permiso_id' => $permissionId,
        ]);

        $this->actingAs($user)
            ->getJson(route('oficios.detalle.json', $oficioId))
            ->assertOk()
            ->assertJsonPath('descripcion', 'Contenido reservado')
            ->assertJsonPath('link_documento', 'https://documento.example.test')
            ->assertJsonPath('link_drive', 'https://drive.example.test')
            ->assertJsonPath('puede_ver_contenido', true);
    }

    public function test_sensitive_file_download_requires_explicit_permission(): void
    {
        Storage::fake('local');
        $oficioId = $this->createSensitiveOffice();
        $userWithoutPermission = $this->createUser();
        $path = 'oficios-sensibles/2026/recibidos/03/documento.pdf';
        Storage::disk('local')->put($path, 'private pdf');

        $archivoId = DB::table('oficio_archivos')->insertGetId([
            'oficio_id' => $oficioId,
            'nombre_original' => 'documento.pdf',
            'tipo_archivo' => 'application/pdf',
            'nivel_acceso' => 'privado',
        ]);

        $versionId = DB::table('oficio_archivo_versiones')->insertGetId([
            'oficio_archivo_id' => $archivoId,
            'ruta' => $path,
            'mime_type' => 'application/pdf',
            'tamano' => 11,
            'hash_sha256' => hash('sha256', 'private pdf'),
            'version' => 1,
            'es_actual' => true,
            'es_publica' => false,
            'subido_por_id' => $userWithoutPermission->id,
        ]);

        $url = route('oficios.archivos.download', [
            $oficioId,
            $versionId,
        ]);

        $this->actingAs($userWithoutPermission)
            ->get($url)
            ->assertForbidden();

        $authorizedUser = $this->createUser();
        $permissionId = DB::table('permisos')->insertGetId([
            'clave' => 'puede_ver_sensibles',
            'nombre' => 'Puede ver sensibles',
        ]);
        DB::table('usuario_permisos')->insert([
            'usuario_id' => $authorizedUser->id,
            'permiso_id' => $permissionId,
        ]);

        $this->actingAs($authorizedUser)
            ->get($url)
            ->assertOk();
    }

    public function test_sensitive_uploads_use_private_storage_and_received_date(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $oficio = Oficio::findOrFail($this->createSensitiveOffice());
        $file = UploadedFile::fake()->create(
            'original.pdf',
            10,
            'application/pdf'
        );

        $path = app(OficioArchivoStorageService::class)
            ->storeUploaded($file, $oficio);

        $this->assertSame(
            'oficios-sensibles/2026/recibidos/03/' . basename($path),
            $path
        );
        Storage::disk('local')->assertExists($path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_sensitive_received_offices_can_be_turned_without_files_or_drive_links(): void
    {
        Mail::fake();
        $oficioId = $this->createSensitiveOffice();
        $user = $this->createUser();
        $this->assignPermission($user, 'puede_turnar');
        $responsableId = DB::table('tipos_participacion')
            ->where('clave', 'responsable')
            ->value('id');
        $apoyoId = DB::table('tipos_participacion')
            ->where('clave', 'apoyo')
            ->value('id');
        $copiadoId = DB::table('tipos_participacion')
            ->where('clave', 'copiado')
            ->value('id');

        $participaciones = [];
        foreach ([$responsableId, $apoyoId, $copiadoId] as $tipoId) {
            $coordinacionId = DB::table('coordinaciones')->insertGetId([
                'clave' => 'COORD-' . uniqid(),
                'nombre' => 'Coordinación de prueba',
                'activo' => true,
            ]);
            $coordinador = $this->createUser();
            $this->assignRole($coordinador, 'coordinador');
            DB::table('coordinacion_user')->insert([
                'coordinacion_id' => $coordinacionId,
                'user_id' => $coordinador->id,
            ]);
            $participaciones[$coordinacionId] = $tipoId;
        }

        $this->actingAs($user)
            ->post(route('oficios.turnar', $oficioId), [
                'participacion' => $participaciones,
            ])
            ->assertRedirect();

        $this->assertSame(
            3,
            DB::table('turnados')->where('oficio_id', $oficioId)->count()
        );
        $this->assertSame(
            3,
            DB::table('notificaciones_turnado')->count()
        );
        $this->assertSame(
            3,
            DB::table('oficios')->where('id', $oficioId)->value('estado_id')
        );
        $this->assertCount(
            3,
            Mail::sent(OficioTurnadoMail::class)
        );

        $correo = Mail::sent(OficioTurnadoMail::class)->first();
        $contenidoCorreo = $correo->render();
        $this->assertStringNotContainsString(
            'Contenido reservado',
            $contenidoCorreo
        );
        $this->assertStringNotContainsString(
            'drive.example.test',
            $contenidoCorreo
        );
        $this->assertStringNotContainsString(
            'documento.example.test',
            $contenidoCorreo
        );

        $revisionId = DB::table('tipos_participacion')
            ->where('clave', 'revision')
            ->value('id');
        $coordinacionId = DB::table('coordinaciones')->insertGetId([
            'clave' => 'REVISION-' . uniqid(),
            'nombre' => 'Coordinación no permitida',
            'activo' => true,
        ]);
        $coordinador = $this->createUser();
        $this->assignRole($coordinador, 'coordinador');
        DB::table('coordinacion_user')->insert([
            'coordinacion_id' => $coordinacionId,
            'user_id' => $coordinador->id,
        ]);

        $this->post(route('oficios.turnar', $oficioId), [
            'participacion' => [
                $coordinacionId => $revisionId,
            ],
        ])->assertRedirect();

        $this->assertSame(
            3,
            DB::table('turnados')->where('oficio_id', $oficioId)->count()
        );
    }

    public function test_sensitive_turnado_can_be_attended_from_the_inbox_without_a_file(): void
    {
        $recipient = $this->createUser();
        $turnadoId = $this->createSensitiveTurnado($recipient);

        $this->actingAs($recipient)
            ->post(route('turnados.atender', $turnadoId))
            ->assertRedirect();

        $turnado = DB::table('turnados')->where('id', $turnadoId)->first();
        $this->assertSame(3, (int) $turnado->estado_turnado_id);
        $this->assertNotNull($turnado->atendido_en);
        $this->assertDatabaseHas('oficios', [
            'id' => $turnado->oficio_id,
            'estado_id' => EstadoOficio::CERRADO,
        ]);
    }

    public function test_sensitive_turnado_can_be_attended_from_a_signed_email_without_a_file(): void
    {
        $recipient = $this->createUser();
        $turnadoId = $this->createSensitiveTurnado($recipient);
        $url = URL::temporarySignedRoute(
            'turnados.atender.correo',
            now()->addMinutes(10),
            ['turnado' => $turnadoId]
        );

        $this->get($url)->assertOk();

        $turnado = DB::table('turnados')->where('id', $turnadoId)->first();
        $this->assertSame(3, (int) $turnado->estado_turnado_id);
        $this->assertNotNull($turnado->atendido_en);
        $this->assertDatabaseHas('oficios', [
            'id' => $turnado->oficio_id,
            'estado_id' => EstadoOficio::CERRADO,
        ]);
    }

    public function test_uploading_a_sensitive_sent_document_does_not_close_the_office(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $oficioId = $this->createSensitiveOffice();
        $oficio = Oficio::findOrFail($oficioId);
        $oficio->update(['tipo_oficio_id' => $this->sentTypeId]);

        $user = $this->createUser();
        $this->assignRole($user, 'admin');
        $this->actingAs($user);
        $oficio->confirmarRelacionNueva();

        $this->post(route('oficios.archivos.store', $oficioId), [
            'archivo' => UploadedFile::fake()->create(
                'original.pdf',
                10,
                'application/pdf'
            ),
        ])->assertRedirect();

        $this->assertSame(
            EstadoOficio::REGISTRADO,
            (int) $oficio->fresh()->estado_id
        );
        $this->assertNull($oficio->fresh()->cerrado_en);
        $this->assertDatabaseHas('oficio_archivo_versiones', [
            'es_publica' => false,
        ]);
    }

    public function test_uploading_an_ordinary_sent_document_keeps_automatic_closure(): void
    {
        Storage::fake('public');
        $oficioId = $this->createSensitiveOffice();
        $oficio = Oficio::findOrFail($oficioId);
        $oficio->update([
            'tipo_oficio_id' => $this->sentTypeId,
            'es_sensible' => false,
        ]);

        $user = $this->createUser();
        $this->assignRole($user, 'admin');
        $this->actingAs($user);
        $oficio->confirmarRelacionNueva();

        $this->post(route('oficios.archivos.store', $oficioId), [
            'archivo' => UploadedFile::fake()->create(
                'ordinary.pdf',
                10,
                'application/pdf'
            ),
        ])->assertRedirect();

        $this->assertSame(
            EstadoOficio::CERRADO,
            (int) $oficio->fresh()->estado_id
        );
        $this->assertNotNull($oficio->fresh()->cerrado_en);
    }

    public function test_administrator_without_content_permission_cannot_remove_sensitive_classification(): void
    {
        $oficioId = $this->createSensitiveOffice();
        $admin = $this->createUser();
        $this->assignRole($admin, 'admin');

        $this->actingAs($admin)
            ->putJson(route('oficios.update', $oficioId), [
                'es_sensible' => false,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('oficios', [
            'id' => $oficioId,
            'es_sensible' => true,
        ]);

        $this->actingAs($admin)
            ->putJson(route('oficios.update', $oficioId), [
                'asunto' => 'Asunto actualizado',
                'descripcion' => 'Contenido que no debe reemplazar al original',
                'fecha_oficio' => '2026-03-04',
                'fecha_recepcion' => '2026-03-04',
                'fecha_limite' => null,
                'requiere_respuesta' => false,
                'es_sensible' => true,
                'remitente_nombre' => 'Remitente',
                'link_documento' => 'https://otro-documento.example.test',
                'link_drive' => 'https://otro-drive.example.test',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('oficios', [
            'id' => $oficioId,
            'asunto' => 'Asunto actualizado',
            'descripcion' => 'Contenido reservado',
            'link_documento' => 'https://documento.example.test',
            'link_drive' => 'https://drive.example.test',
        ]);
    }

    public function test_public_versions_block_classifying_an_office_as_sensitive(): void
    {
        $oficioId = $this->createSensitiveOffice();
        DB::table('oficios')
            ->where('id', $oficioId)
            ->update(['es_sensible' => false]);

        $user = $this->createUser();
        $this->assignRole($user, 'admin');
        $archivoId = DB::table('oficio_archivos')->insertGetId([
            'oficio_id' => $oficioId,
            'nombre_original' => 'publico.pdf',
            'tipo_archivo' => 'application/pdf',
            'nivel_acceso' => 'publico',
        ]);
        DB::table('oficio_archivo_versiones')->insert([
            'oficio_archivo_id' => $archivoId,
            'ruta' => 'oficios/1/publico.pdf',
            'mime_type' => 'application/pdf',
            'tamano' => 10,
            'hash_sha256' => hash('sha256', 'public'),
            'version' => 1,
            'es_actual' => true,
            'es_publica' => true,
            'subido_por_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->putJson(route('oficios.update', $oficioId), [
                'es_sensible' => true,
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('oficios', [
            'id' => $oficioId,
            'es_sensible' => false,
        ]);
    }

    public function test_mark_as_sent_button_only_appears_for_authorized_eligible_offices(): void
    {
        $admin = $this->createUser();
        $this->assignRole($admin, 'admin');
        $registrar = $this->createUser();
        $this->assignPermission($registrar, 'puede_registrar_oficios');
        $unauthorized = $this->createUser();

        $eligibleId = $this->createSensitiveSentOffice(false);
        $alreadySentId = $this->createSensitiveSentOffice(true);
        DB::table('oficio_historial')->insert([
            'oficio_id' => $alreadySentId,
            'usuario_id' => $admin->id,
            'accion' => Oficio::ACCION_MARCADO_COMO_ENVIADO,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $receivedId = $this->createSensitiveOffice();
        $ordinaryId = $this->createSensitiveSentOffice(false);
        DB::table('oficios')
            ->where('id', $ordinaryId)
            ->update(['es_sensible' => false]);

        $this->assertStringContainsString(
            'Marcar como enviado',
            $this->renderOfficeRow($eligibleId, $admin)
        );
        $this->assertStringContainsString(
            'Marcar como enviado',
            $this->renderOfficeRow($eligibleId, $registrar)
        );
        $this->assertStringNotContainsString(
            'Marcar como enviado',
            $this->renderOfficeRow($eligibleId, $unauthorized)
        );
        $this->assertStringNotContainsString(
            'Marcar como enviado',
            $this->renderOfficeRow($alreadySentId, $admin)
        );
        $this->assertStringNotContainsString(
            'Marcar como enviado',
            $this->renderOfficeRow($receivedId, $admin)
        );
        $this->assertStringNotContainsString(
            'Marcar como enviado',
            $this->renderOfficeRow($ordinaryId, $admin)
        );
    }

    public function test_mark_as_sent_closes_offices_without_response_and_is_idempotent(): void
    {
        $admin = $this->createUser();
        $this->assignRole($admin, 'admin');
        $oficioId = $this->createSensitiveSentOffice(false);

        $this->assertStringContainsString(
            'Marcar como enviado',
            $this->renderOfficeRow($oficioId, $admin)
        );

        $url = route('oficios.marcar-enviado', $oficioId);
        $this->actingAs($admin)->post($url)->assertRedirect();

        $oficio = Oficio::findOrFail($oficioId);
        $this->assertSame(EstadoOficio::CERRADO, (int) $oficio->estado_id);
        $this->assertNotNull($oficio->cerrado_en);

        $evento = DB::table('oficio_historial')
            ->where('oficio_id', $oficioId)
            ->where('accion', Oficio::ACCION_MARCADO_COMO_ENVIADO)
            ->first();
        $this->assertSame($admin->id, (int) $evento->usuario_id);
        $this->assertSame(
            EstadoOficio::REGISTRADO,
            (int) $evento->estado_anterior_id
        );
        $this->assertSame(
            EstadoOficio::CERRADO,
            (int) $evento->estado_nuevo_id
        );
        $this->assertNotNull($evento->created_at);
        $this->assertStringNotContainsString(
            'Marcar como enviado',
            $this->renderOfficeRow($oficioId, $admin)
        );

        $this->post($url)->assertRedirect();
        $this->assertSame(
            1,
            DB::table('oficio_historial')
                ->where('oficio_id', $oficioId)
                ->where('accion', Oficio::ACCION_MARCADO_COMO_ENVIADO)
                ->count()
        );
    }

    public function test_mark_as_sent_keeps_offices_requiring_a_response_in_follow_up_and_hides_button(): void
    {
        $registrar = $this->createUser();
        $this->assignPermission($registrar, 'puede_registrar_oficios');
        $oficioId = $this->createSensitiveSentOffice(true);

        $this->assertStringContainsString(
            'Marcar como enviado',
            $this->renderOfficeRow($oficioId, $registrar)
        );

        $this->actingAs($registrar)
            ->post(route('oficios.marcar-enviado', $oficioId))
            ->assertRedirect();

        $oficio = Oficio::findOrFail($oficioId);
        $this->assertSame(
            EstadoOficio::EN_SEGUIMIENTO,
            (int) $oficio->estado_id
        );
        $this->assertNull($oficio->cerrado_en);
        $this->assertStringNotContainsString(
            'Marcar como enviado',
            $this->renderOfficeRow($oficioId, $registrar)
        );

        $this->assertSame(
            1,
            DB::table('oficio_historial')
                ->where('oficio_id', $oficioId)
                ->where('accion', Oficio::ACCION_MARCADO_COMO_ENVIADO)
                ->count()
        );
    }

    public function test_mark_as_sent_route_rejects_unauthorized_and_ineligible_offices(): void
    {
        $unauthorized = $this->createUser();
        $admin = $this->createUser();
        $this->assignRole($admin, 'admin');
        $eligibleId = $this->createSensitiveSentOffice(false);
        $receivedId = $this->createSensitiveOffice();
        $ordinaryId = $this->createSensitiveSentOffice(false);
        DB::table('oficios')
            ->where('id', $ordinaryId)
            ->update(['es_sensible' => false]);

        $this->actingAs($unauthorized)
            ->post(route('oficios.marcar-enviado', $eligibleId))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('oficios.marcar-enviado', $receivedId))
            ->assertNotFound();

        $this->post(route('oficios.marcar-enviado', $ordinaryId))
            ->assertNotFound();

        $this->assertSame(
            0,
            DB::table('oficio_historial')
                ->where('accion', Oficio::ACCION_MARCADO_COMO_ENVIADO)
                ->count()
        );
    }

    public function test_registering_a_response_closes_a_sensitive_follow_up_petition_once(): void
    {
        $registrar = $this->createUser();
        $this->assignPermission($registrar, 'puede_registrar_oficios');
        $peticionId = $this->createSensitiveSentOffice(
            true,
            EstadoOficio::EN_SEGUIMIENTO
        );

        $payload = $this->responsePayload($peticionId);
        $this->actingAs($registrar)
            ->post(route('oficios.store'), $payload)
            ->assertRedirect();

        $respuestaId = (int) DB::table('oficios')
            ->where('respuesta_a_oficio_id', $peticionId)
            ->value('id');
        $this->assertGreaterThan(0, $respuestaId);
        $this->assertDatabaseHas('oficios', [
            'id' => $peticionId,
            'estado_id' => EstadoOficio::CERRADO,
        ]);
        $this->assertNotNull(
            DB::table('oficios')->where('id', $peticionId)->value('cerrado_en')
        );

        $eventoQuery = DB::table('oficio_historial')
            ->where('oficio_id', $peticionId)
            ->where('accion', 'oficio_cerrado_por_respuesta');
        $evento = $eventoQuery->first();
        $this->assertSame($registrar->id, (int) $evento->usuario_id);
        $this->assertSame(Oficio::class, $evento->entidad_relacionada);
        $this->assertSame(
            $respuestaId,
            (int) $evento->entidad_relacionada_id
        );

        $this->post(route('oficios.store'), array_merge(
            $payload,
            ['numero_oficio' => 'RESPUESTA-2']
        ))->assertRedirect();

        $this->assertSame(1, $eventoQuery->count());
    }

    public function test_generic_office_relation_does_not_close_a_sensitive_petition(): void
    {
        $peticionId = $this->createSensitiveSentOffice(
            true,
            EstadoOficio::EN_SEGUIMIENTO
        );
        $respuestaORelacionadaId = $this->createSensitiveOffice();
        DB::table('oficio_relaciones')->insert([
            'oficio_origen_id' => $peticionId,
            'oficio_relacionado_id' => $respuestaORelacionadaId,
            'tipo_relacion_id' => 1,
            'usuario_registro_id' => DB::table('oficios')
                ->where('id', $respuestaORelacionadaId)
                ->value('usuario_registro_id'),
        ]);

        $this->assertDatabaseHas('oficios', [
            'id' => $peticionId,
            'estado_id' => EstadoOficio::EN_SEGUIMIENTO,
            'cerrado_en' => null,
        ]);
        $this->assertSame(
            0,
            DB::table('oficio_historial')
                ->where('oficio_id', $peticionId)
                ->where('accion', 'oficio_cerrado_por_respuesta')
                ->count()
        );
    }

    public function test_ordinary_response_closure_behavior_remains_unchanged(): void
    {
        $registrar = $this->createUser();
        $this->assignPermission($registrar, 'puede_registrar_oficios');
        $peticionId = $this->createSensitiveSentOffice(
            true,
            EstadoOficio::EN_SEGUIMIENTO
        );
        DB::table('oficios')
            ->where('id', $peticionId)
            ->update(['es_sensible' => false]);

        $this->actingAs($registrar)
            ->post(route('oficios.store'), $this->responsePayload($peticionId))
            ->assertRedirect();

        $this->assertDatabaseHas('oficios', [
            'id' => $peticionId,
            'estado_id' => EstadoOficio::CERRADO,
        ]);
        $this->assertSame(
            1,
            DB::table('oficio_historial')
                ->where('oficio_id', $peticionId)
                ->where('accion', 'oficio_cerrado_automaticamente')
                ->count()
        );
    }

    private function createUser(): User
    {
        $id = DB::table('users')->insertGetId([
            'name' => 'Test User',
            'email' => uniqid('user') . '@example.test',
            'password' => bcrypt('password'),
        ]);

        return User::findOrFail($id);
    }

    private function createSensitiveOffice(): int
    {
        $user = $this->createUser();
        $officeId = DB::table('oficios')->insertGetId([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'numero_oficio' => 'SCO-TEST-1',
            'tipo_oficio_id' => $this->receivedTypeId,
            'estado_id' => $this->registeredStateId,
            'asunto' => 'Asunto administrativo',
            'descripcion' => 'Contenido reservado',
            'fecha_oficio' => '2026-03-04',
            'fecha_recepcion' => '2026-03-04',
            'requiere_respuesta' => false,
            'es_sensible' => true,
            'remitente_nombre' => 'Remitente',
            'usuario_registro_id' => $user->id,
            'link_documento' => 'https://documento.example.test',
            'link_drive' => 'https://drive.example.test',
        ]);

        $tagId = DB::table('tags')->insertGetId([
            'nombre' => 'reservado',
        ]);
        DB::table('oficio_tag')->insert([
            'oficio_id' => $officeId,
            'tag_id' => $tagId,
        ]);

        return $officeId;
    }

    private function createSensitiveSentOffice(
        bool $requiresResponse,
        int $stateId = EstadoOficio::REGISTRADO
    ): int {
        $officeId = $this->createSensitiveOffice();

        DB::table('oficios')
            ->where('id', $officeId)
            ->update([
                'tipo_oficio_id' => $this->sentTypeId,
                'requiere_respuesta' => $requiresResponse,
                'estado_id' => $stateId,
            ]);

        return $officeId;
    }

    private function renderOfficeRow(int $officeId, User $user): string
    {
        $oficio = Oficio::with([
            'tipo',
            'estado',
            'tags',
            'turnados.usuario',
            'turnados.tipoParticipacion',
            'turnados.estadoTurnado',
            'turnados.notificacion',
        ])
            ->withCount([
                'historial as marcado_como_enviado_count' => function ($query) {
                    $query->where(
                        'accion',
                        Oficio::ACCION_MARCADO_COMO_ENVIADO
                    );
                },
            ])
            ->findOrFail($officeId);

        $this->actingAs($user);

        return view(
            'oficios.partials.oficio-row',
            compact('oficio')
        )->render();
    }

    private function responsePayload(int $peticionId): array
    {
        return [
            'tipo_oficio_id' => $this->receivedTypeId,
            'numero_oficio' => 'RESPUESTA-' . uniqid(),
            'asunto' => 'Respuesta de prueba',
            'fecha_oficio' => '2026-03-10',
            'fecha_recepcion' => '2026-03-10',
            'tipo_relacion' => 'relacionado',
            'respuesta_a_oficio_id' => $peticionId,
            'link_drive' => 'https://drive.example.test/respuesta',
        ];
    }

    private function createSensitiveTurnado(User $recipient): int
    {
        $oficioId = $this->createSensitiveOffice();
        $oficio = Oficio::findOrFail($oficioId);
        $oficio->update(['estado_id' => EstadoOficio::TURNADO]);

        $coordinacionId = DB::table('coordinaciones')->insertGetId([
            'clave' => 'ATENCION-' . uniqid(),
            'nombre' => 'Coordinación responsable',
            'activo' => true,
        ]);

        return DB::table('turnados')->insertGetId([
            'oficio_id' => $oficioId,
            'usuario_id' => $recipient->id,
            'coordinacion_id' => $coordinacionId,
            'tipo_participacion_id' => DB::table('tipos_participacion')
                ->where('clave', 'responsable')
                ->value('id'),
            'estado_turnado_id' => DB::table('estados_turnado')
                ->where('clave', 'activo')
                ->value('id'),
            'turnado_por_id' => $recipient->id,
            'turnado_en' => now(),
            'es_principal' => false,
        ]);
    }

    private function assignRole(User $user, string $role): void
    {
        $roleId = DB::table('roles')->insertGetId([
            'clave' => $role,
            'nombre' => ucfirst($role),
        ]);

        DB::table('usuario_roles')->insert([
            'usuario_id' => $user->id,
            'rol_id' => $roleId,
        ]);
    }

    private function assignPermission(User $user, string $permission): void
    {
        $permissionId = DB::table('permisos')->insertGetId([
            'clave' => $permission,
            'nombre' => ucfirst($permission),
        ]);

        DB::table('usuario_permisos')->insert([
            'usuario_id' => $user->id,
            'permiso_id' => $permissionId,
        ]);
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('nombre');
            $table->timestamps();
        });

        foreach ([
            'usuario_roles' => ['usuario_id', 'rol_id'],
            'usuario_permisos' => ['usuario_id', 'permiso_id'],
            'rol_permiso' => ['rol_id', 'permiso_id'],
        ] as $tableName => $columns) {
            Schema::create($tableName, function (Blueprint $table) use ($columns) {
                $table->id();
                $table->unsignedBigInteger($columns[0]);
                $table->unsignedBigInteger($columns[1]);
                $table->timestamps();
            });
        }

        Schema::create('tipos_oficio', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('nombre');
            $table->boolean('activo');
        });

        Schema::create('estados_oficio', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('nombre');
            $table->string('color');
            $table->integer('orden');
            $table->boolean('es_final');
            $table->boolean('activo');
        });

        Schema::create('tipos_participacion', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('nombre');
            $table->boolean('implica_responsabilidad');
            $table->boolean('activo');
        });

        Schema::create('estados_turnado', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('nombre');
            $table->boolean('activo');
        });

        Schema::create('coordinaciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('nombre');
            $table->boolean('activo');
        });

        Schema::create('coordinacion_user', function (Blueprint $table) {
            $table->unsignedBigInteger('coordinacion_id');
            $table->unsignedBigInteger('user_id');
        });

        Schema::create('oficios', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('numero_oficio')->nullable();
            $table->string('consecutivo')->nullable();
            $table->unsignedBigInteger('tipo_oficio_id');
            $table->unsignedBigInteger('estado_id');
            $table->string('asunto');
            $table->text('descripcion')->nullable();
            $table->date('fecha_oficio');
            $table->date('fecha_recepcion');
            $table->date('fecha_limite')->nullable();
            $table->boolean('requiere_respuesta');
            $table->boolean('es_sensible');
            $table->unsignedBigInteger('respuesta_a_oficio_id')->nullable();
            $table->string('remitente_nombre')->nullable();
            $table->string('remitente_cargo')->nullable();
            $table->string('remitente_dependencia')->nullable();
            $table->string('destinatario_nombre')->nullable();
            $table->string('destinatario_cargo')->nullable();
            $table->string('destinatario_dependencia')->nullable();
            $table->string('quien_elabora_nombre')->nullable();
            $table->string('quien_elabora_cargo')->nullable();
            $table->unsignedBigInteger('responsable_inicial_id')->nullable();
            $table->unsignedBigInteger('coordinacion_origen_id')->nullable();
            $table->unsignedBigInteger('usuario_registro_id');
            $table->string('link_documento')->nullable();
            $table->string('link_drive')->nullable();
            $table->timestamp('respondido_en')->nullable();
            $table->timestamp('cerrado_en')->nullable();
            $table->timestamp('cancelado_en')->nullable();
            $table->timestamps();
        });

        Schema::create('turnados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('oficio_id');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('coordinacion_id');
            $table->unsignedBigInteger('tipo_participacion_id');
            $table->unsignedBigInteger('estado_turnado_id');
            $table->unsignedBigInteger('turnado_por_id');
            $table->timestamp('turnado_en')->nullable();
            $table->timestamp('atendido_en')->nullable();
            $table->timestamp('cerrado_en')->nullable();
            $table->boolean('es_principal')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('oficio_historial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('oficio_id');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('accion');
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('estado_anterior_id')->nullable();
            $table->unsignedBigInteger('estado_nuevo_id')->nullable();
            $table->string('entidad_relacionada')->nullable();
            $table->unsignedBigInteger('entidad_relacionada_id')->nullable();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('oficio_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('oficio_id');
            $table->unsignedBigInteger('tag_id');
        });

        Schema::create('oficio_archivos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('oficio_id');
            $table->string('nombre_original');
            $table->string('tipo_archivo');
            $table->string('nivel_acceso');
            $table->timestamps();
        });

        Schema::create('oficio_archivo_versiones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('oficio_archivo_id');
            $table->string('ruta');
            $table->string('mime_type');
            $table->unsignedBigInteger('tamano');
            $table->string('hash_sha256');
            $table->unsignedInteger('version');
            $table->boolean('es_actual');
            $table->boolean('es_publica');
            $table->unsignedBigInteger('subido_por_id');
            $table->text('motivo_reemplazo')->nullable();
            $table->unsignedBigInteger('version_anterior_id')->nullable();
            $table->timestamps();
        });

        Schema::create('oficio_relaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('oficio_origen_id');
            $table->unsignedBigInteger('oficio_relacionado_id');
            $table->unsignedBigInteger('tipo_relacion_id');
            $table->unsignedBigInteger('usuario_registro_id');
            $table->timestamps();
        });

        Schema::create('notificaciones_turnado', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('turnado_id');
            $table->string('destinatario_email');
            $table->string('estado');
            $table->string('message_id')->nullable();
            $table->unsignedInteger('intentos')->default(0);
            $table->timestamp('ultimo_intento_en')->nullable();
            $table->timestamp('enviado_en')->nullable();
            $table->timestamp('no_entregado_en')->nullable();
            $table->timestamp('notificado_manualmente_en')->nullable();
            $table->unsignedBigInteger('notificado_manualmente_por_id')->nullable();
            $table->text('ultimo_error')->nullable();
            $table->timestamps();
        });
    }
}
