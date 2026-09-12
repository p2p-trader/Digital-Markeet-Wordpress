import { NextResponse } from 'next/server';
import path from 'path';
import fs from 'fs';
import { ZipArchive } from 'archiver';

export const dynamic = 'force-dynamic';

export async function GET() {
  try {
    const wordpressDir = path.resolve(process.cwd(), 'wordpress');

    if (!fs.existsSync(wordpressDir)) {
      return NextResponse.json(
        { error: `The wordpress directory was not found at ${wordpressDir}` },
        { status: 404 }
      );
    }

    const stat = fs.statSync(wordpressDir);
    if (!stat.isDirectory()) {
      return NextResponse.json(
        { error: 'Specified path is not a directory.' },
        { status: 400 }
      );
    }

    // Prepare in-memory buffer archive using archiver's ZipArchive
    const archive = new ZipArchive({
      zlib: { level: 9 }, // Maximum compression
    });

    const chunks: Buffer[] = [];

    archive.on('data', (chunk: Buffer) => {
      chunks.push(chunk);
    });

    const archivePromise = new Promise<Buffer>((resolve, reject) => {
      archive.on('end', () => {
        resolve(Buffer.concat(chunks));
      });

      archive.on('error', (err: Error) => {
        reject(err);
      });
    });

    // Append all contents of the wordpress folder, preserving internal directory structure
    archive.directory(wordpressDir, false);

    // Finalize the archive stream
    await archive.finalize();

    const zipBuffer = await archivePromise;

    return new Response(new Uint8Array(zipBuffer), {
      status: 200,
      headers: {
        'Content-Type': 'application/zip',
        'Content-Disposition': 'attachment; filename="wordpress-theme.zip"',
        'Content-Length': zipBuffer.length.toString(),
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache',
        'Expires': '0',
      },
    });
  } catch (error: unknown) {
    const message = error instanceof Error ? error.message : 'Unknown server error during archive creation.';
    return NextResponse.json(
      { error: `Failed to generate zip file: ${message}` },
      { status: 500 }
    );
  }
}
