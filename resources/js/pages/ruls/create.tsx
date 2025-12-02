import AppLayout from '@/layouts/app-layout';
import { index, store } from '@/routes/ruls';
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
import { ArrowLeft, Upload, X } from 'lucide-react';
import { useRef, useState } from 'react';
import SignatureCanvas from 'react-signature-canvas';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Resource Unit Leaders',
        href: index().url,
    },
    {
        title: 'Create',
        href: '#',
    },
];

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        contact_number: '',
        serial_number: '',
        department: '',
        certificates: [] as File[],
        signature: '',
    });

    const signatureRef = useRef<SignatureCanvas>(null);
    const [certificateFiles, setCertificateFiles] = useState<File[]>([]);

    const handleCertificateUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files) {
            const newFiles = Array.from(e.target.files);
            const updatedFiles = [...certificateFiles, ...newFiles];
            setCertificateFiles(updatedFiles);
            setData('certificates', updatedFiles);
        }
    };

    const removeCertificate = (index: number) => {
        const updatedFiles = certificateFiles.filter((_, i) => i !== index);
        setCertificateFiles(updatedFiles);
        setData('certificates', updatedFiles);
    };

    const clearSignature = () => {
        signatureRef.current?.clear();
        setData('signature', '');
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        // Capture signature before submitting
        if (signatureRef.current && !signatureRef.current.isEmpty()) {
            const signatureData = signatureRef.current.toDataURL();
            data.signature = signatureData;
        }

        post(store().url, {
            forceFormData: true,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Resource Unit Leader" />
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
                                <CardTitle>Create Resource Unit Leader</CardTitle>
                                <CardDescription>
                                    Add a new resource unit leader to the system
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
                                <Label htmlFor="certificates">
                                    Certificates <span className="text-gray-500">(Optional)</span>
                                </Label>
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
                                                    onClick={() => removeCertificate(index)}
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
                                    {processing ? 'Creating...' : 'Create Resource Unit Leader'}
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
