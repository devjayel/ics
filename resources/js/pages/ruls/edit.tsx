import AppLayout from '@/layouts/app-layout';
import { index, update } from '@/routes/ruls';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { ArrowLeft, Upload, X, Download } from 'lucide-react';
import { useRef, useState } from 'react';
import SignatureCanvas from 'react-signature-canvas';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Resource Unit Leaders',
        href: index().url,
    },
    {
        title: 'Edit',
        href: '#',
    },
];

interface Certificate {
    uuid: string;
    certificate_name: string;
    file_path: string;
}

interface Rul {
    uuid: string;
    name: string;
    contact_number: string;
    serial_number: string;
    department: string;
    signature?: string;
    certificates: Certificate[];
}

interface EditProps {
    rul: Rul;
}

export default function Edit({ rul }: EditProps) {
    const { data, setData, post, processing, errors } = useForm({
        name: rul.name,
        contact_number: rul.contact_number,
        serial_number: rul.serial_number,
        department: rul.department,
        certificates: [] as File[],
        signature: '',
        remove_certificates: [] as string[],
        remove_signature: false,
        _method: 'PUT',
    });

    const signatureRef = useRef<SignatureCanvas>(null);
    const [certificateFiles, setCertificateFiles] = useState<File[]>([]);
    const [existingCertificates, setExistingCertificates] = useState<Certificate[]>(
        rul.certificates
    );
    const [certificatesToRemove, setCertificatesToRemove] = useState<string[]>([]);
    const [hasSignature, setHasSignature] = useState<boolean>(!!rul.signature);
    const [signatureRemoved, setSignatureRemoved] = useState<boolean>(false);

    const handleCertificateUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files) {
            const newFiles = Array.from(e.target.files);
            const updatedFiles = [...certificateFiles, ...newFiles];
            setCertificateFiles(updatedFiles);
            setData('certificates', updatedFiles);
        }
    };

    const removeNewCertificate = (index: number) => {
        const updatedFiles = certificateFiles.filter((_, i) => i !== index);
        setCertificateFiles(updatedFiles);
        setData('certificates', updatedFiles);
    };

    const removeExistingCertificate = (uuid: string) => {
        setExistingCertificates(existingCertificates.filter((cert) => cert.uuid !== uuid));
        const updatedRemoveList = [...certificatesToRemove, uuid];
        setCertificatesToRemove(updatedRemoveList);
        setData('remove_certificates', updatedRemoveList);
    };

    const clearSignature = () => {
        signatureRef.current?.clear();
        setData('signature', '');
    };

    const removeSignature = () => {
        setHasSignature(false);
        setSignatureRemoved(true);
        setData('remove_signature', true);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        // Capture signature before submitting
        if (signatureRef.current && !signatureRef.current.isEmpty()) {
            const signatureData = signatureRef.current.toDataURL();
            data.signature = signatureData;
        }

        post(update({ rul: rul.uuid }).url, {
            forceFormData: true,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Resource Unit Leader" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-4">
                            <Link href={index().url}>
                                <Button variant="outline" size="icon">
                                    <ArrowLeft className="h-4 w-4" />
                                </Button>
                            </Link>
                            <div>
                                <CardTitle>Edit Resource Unit Leader</CardTitle>
                                <CardDescription>
                                    Update resource unit leader information
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="name">
                                        Name <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        placeholder="Enter name"
                                    />
                                    {errors.name && (
                                        <p className="text-sm text-red-500">{errors.name}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="serial_number">
                                        Serial Number <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="serial_number"
                                        value={data.serial_number}
                                        onChange={(e) =>
                                            setData('serial_number', e.target.value)
                                        }
                                        placeholder="Enter serial number"
                                    />
                                    {errors.serial_number && (
                                        <p className="text-sm text-red-500">
                                            {errors.serial_number}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="contact_number">
                                        Contact Number <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="contact_number"
                                        value={data.contact_number}
                                        onChange={(e) =>
                                            setData('contact_number', e.target.value)
                                        }
                                        placeholder="Enter contact number"
                                    />
                                    {errors.contact_number && (
                                        <p className="text-sm text-red-500">
                                            {errors.contact_number}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="department">
                                        Department <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="department"
                                        value={data.department}
                                        onChange={(e) => setData('department', e.target.value)}
                                        placeholder="Enter department"
                                    />
                                    {errors.department && (
                                        <p className="text-sm text-red-500">
                                            {errors.department}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label>
                                    Certificates <span className="text-gray-500">(Optional)</span>
                                </Label>

                                {existingCertificates.length > 0 && (
                                    <div className="mb-4 space-y-2">
                                        <p className="text-sm font-medium">Existing Certificates</p>
                                        {existingCertificates.map((cert) => (
                                            <div
                                                key={cert.uuid}
                                                className="flex items-center justify-between rounded-md border bg-gray-50 p-2"
                                            >
                                                <div className="flex items-center gap-2">
                                                    <span className="text-sm">
                                                        {cert.certificate_name}
                                                    </span>
                                                    <a
                                                        href={`/storage/${cert.file_path}`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="text-blue-500 hover:text-blue-700"
                                                    >
                                                        <Download className="h-4 w-4" />
                                                    </a>
                                                </div>
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() =>
                                                        removeExistingCertificate(cert.uuid)
                                                    }
                                                >
                                                    <X className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        ))}
                                    </div>
                                )}

                                <div className="flex items-center gap-2">
                                    <Input
                                        id="certificates"
                                        type="file"
                                        multiple
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        onChange={handleCertificateUpload}
                                        className="cursor-pointer"
                                    />
                                    <Upload className="h-5 w-5 text-gray-400" />
                                </div>
                                {errors.certificates && (
                                    <p className="text-sm text-red-500">{errors.certificates}</p>
                                )}
                                {certificateFiles.length > 0 && (
                                    <div className="mt-2 space-y-2">
                                        <p className="text-sm font-medium">New Certificates</p>
                                        {certificateFiles.map((file, index) => (
                                            <div
                                                key={index}
                                                className="flex items-center justify-between rounded-md border p-2"
                                            >
                                                <span className="text-sm">{file.name}</span>
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => removeNewCertificate(index)}
                                                >
                                                    <X className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label>
                                    Signature <span className="text-gray-500">(Optional)</span>
                                </Label>

                                {hasSignature && !signatureRemoved && (
                                    <div className="mb-4 space-y-2">
                                        <p className="text-sm font-medium">Existing Signature</p>
                                        <div className="flex items-center gap-4 rounded-md border p-4">
                                            <img
                                                src={`/storage/${rul.signature}`}
                                                alt="Signature"
                                                className="h-24 border"
                                            />
                                            <Button
                                                type="button"
                                                variant="destructive"
                                                size="sm"
                                                onClick={removeSignature}
                                            >
                                                Remove Signature
                                            </Button>
                                        </div>
                                    </div>
                                )}

                                <div className="rounded-md border">
                                    <SignatureCanvas
                                        ref={signatureRef}
                                        canvasProps={{
                                            className: 'w-full h-48 cursor-crosshair',
                                        }}
                                    />
                                </div>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    onClick={clearSignature}
                                >
                                    Clear Signature
                                </Button>
                                {errors.signature && (
                                    <p className="text-sm text-red-500">{errors.signature}</p>
                                )}
                            </div>

                            <div className="flex gap-2">
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Updating...' : 'Update Resource Unit Leader'}
                                </Button>
                                <Link href={index().url}>
                                    <Button type="button" variant="outline">
                                        Cancel
                                    </Button>
                                </Link>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
